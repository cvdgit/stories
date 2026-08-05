import { useEffect, useMemo, useRef, useState } from 'react';
import { streamLangServeResponse } from '../services/langserveService';

const initialMessages = [
  {
    role: 'ai',
    text: 'Привет! Я могу помочь собрать идеи, выделить ключевые темы и подготовить структуру для ментальной карты.'
  }
];

export function useChat(mentalMapId = 'id_mental_map') {
  const [messages, setMessages] = useState(initialMessages);
  const [draft, setDraft] = useState('');
  const [isSidebarOpen, setIsSidebarOpen] = useState(false);
  const [isLoading, setIsLoading] = useState(false);
  const [error, setError] = useState('');
  const [hasLoadedHistory, setHasLoadedHistory] = useState(false);
  const fileInputRef = useRef(null);
  const historyCacheRef = useRef({});

  const openSidebar = () => setIsSidebarOpen(true);
  const closeSidebar = () => setIsSidebarOpen(false);

  const mapItems = useMemo(() => {
    const lastAiMessage = [...messages].reverse().find((message) => message.role === 'ai');
    if (!lastAiMessage) {
      return ['Ключевая идея'];
    }

    return (
      lastAiMessage.text
        .split(/(?<=[.!?])\s+/)
        .filter((part) => part.trim().length > 0)
        .slice(0, 4)
        .map((part) => part.trim()) || ['Ключевая идея']
    );
  }, [messages]);

  useEffect(() => {
    if (hasLoadedHistory) {
      return;
    }

    let isCancelled = false;

    const loadConversationHistory = async () => {
      try {
        const cachedHistory = historyCacheRef.current[mentalMapId];
        if (cachedHistory) {
          if (!isCancelled) {
            setMessages(cachedHistory);
            setHasLoadedHistory(true);
          }
          return;
        }

        const response = await fetch(`/api/conversations/${mentalMapId}`);
        if (!response.ok) {
          throw new Error('Не удалось загрузить историю беседы');
        }

        const payload = await response.json();
        const history = Array.isArray(payload?.messages)
          ? payload.messages.map((item) => ({
            role: item.role === 'assistant' ? 'ai' : item.role,
            text: item.text || item.content || ''
          }))
          : initialMessages;

        if (!isCancelled) {
          historyCacheRef.current[mentalMapId] = history;
          setMessages(history);
          setHasLoadedHistory(true);
        }
      } catch (requestError) {
        if (!isCancelled) {
          setError(requestError instanceof Error ? requestError.message : 'Неизвестная ошибка');
          setHasLoadedHistory(true);
        }
      }
    };

    loadConversationHistory();

    return () => {
      isCancelled = true;
    };
  }, [hasLoadedHistory, mentalMapId]);

  const streamResponse = async (input, fallbackErrorMessage) => {
    await streamLangServeResponse(input, (deltaText) => {
      setMessages((current) => {
        const next = [...current];
        const lastMessage = next[next.length - 1];
        if (lastMessage?.role === 'ai') {
          lastMessage.text = `${lastMessage.text || ''}${deltaText}`;
        } else {
          next.push({ role: 'ai', text: deltaText });
        }
        return next;
      });
    });

    if (!input) {
      throw new Error(fallbackErrorMessage);
    }
  };

  const handleSubmit = async (event) => {
    event.preventDefault();
    const trimmed = draft.trim();
    if (!trimmed) {
      return;
    }

    const userMessage = { role: 'user', text: trimmed };
    setMessages((current) => [...current, userMessage, { role: 'ai', text: '' }]);
    setDraft('');
    openSidebar();
    setIsLoading(true);
    setError('');

    try {
      await streamResponse(
        trimmed,
        'Не удалось получить ответ от нейросети. Проверьте, что LangServe запущен и доступен.'
      );
    } catch (requestError) {
      setMessages((current) => {
        const next = [...current];
        const lastMessage = next[next.length - 1];
        if (lastMessage?.role === 'ai') {
          lastMessage.text = 'Не удалось получить ответ от нейросети. Проверьте, что LangServe запущен и доступен.';
        }
        return next;
      });
      setError(requestError instanceof Error ? requestError.message : 'Неизвестная ошибка');
    } finally {
      setIsLoading(false);
    }
  };

  const handleAttachFile = async (event) => {
    const file = event.target.files?.[0];
    if (!file) {
      return;
    }

    setMessages((current) => [...current, { role: 'user', text: `Прикреплён файл: ${file.name}` }, { role: 'ai', text: '' }]);
    setIsLoading(true);
    setError('');

    try {
      await streamResponse(
        `Проанализируй файл ${file.name} и кратко опиши, как его можно использовать для ментальной карты.`,
        'Не удалось обработать файл через нейросеть.'
      );
    } catch (requestError) {
      setMessages((current) => {
        const next = [...current];
        const lastMessage = next[next.length - 1];
        if (lastMessage?.role === 'ai') {
          lastMessage.text = 'Не удалось обработать файл через нейросеть.';
        }
        return next;
      });
      setError(requestError instanceof Error ? requestError.message : 'Неизвестная ошибка');
    } finally {
      setIsLoading(false);
    }

    event.target.value = '';
    openSidebar();
  };

  return {
    messages,
    draft,
    setDraft,
    isSidebarOpen,
    isLoading,
    error,
    fileInputRef,
    mapItems,
    handleSubmit,
    handleAttachFile,
    openSidebar,
    closeSidebar
  };
}
