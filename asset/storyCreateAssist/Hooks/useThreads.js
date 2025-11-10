import {useEffect, useState} from "react";
import api from "../Api";
import {useQueryState} from "nuqs";

export function useThreads(userId) {
  const [isUserThreadsLoading, setIsUserThreadsLoading] = useState(false);
  const [userThreads, setUserThreads] = useState([]);
  const [threadId, setThreadId] = useQueryState('id');

  useEffect(() => {
    if (typeof window === 'undefined' || !userId) return;
    getUserThreads(userId);
  }, [userId]);

  const getUserThreads = async (id) => {
    setIsUserThreadsLoading(true);
    try {
      const userThreads = await api.get(`/admin/index.php?r=story-ai/threads`);
      if (userThreads.length > 0) {
        setUserThreads(userThreads);
      }
    } finally {
      setIsUserThreadsLoading(false);
    }
  };

  const getThreadById = async (id) => {
    return api.get(`/admin/index.php?r=story-ai/state&id=${id}`)
  };

  /**
   * @param {string} id
   * @param {() => {}} clearMessages
   * @returns {Promise<void>}
   */
  const deleteThread = async (id, clearMessages) => {
    if (!userId) {
      throw new Error("User ID not found");
    }
    setUserThreads((prevThreads) => prevThreads.filter(thread => thread.id !== id));

    await api.post('/admin/index.php?r=story-ai/delete-thread', {threadId: id}, {
      'X-CSRF-Token': document.querySelector('meta[name=csrf-token]').getAttribute('content')
    })

    if (id === threadId) {
      clearMessages();
      getUserThreads(userId);
      setThreadId(null);
    }
  };

  const createThread = async (data) => {
    const newThread = await api.post('/admin/index.php?r=story-ai/create-thread', data, {
      'X-CSRF-Token': document.querySelector('meta[name=csrf-token]').getAttribute('content')
    });
    setUserThreads(prevThreads => [newThread, ...prevThreads]);
    return new Promise((resolve, reject) => resolve(newThread));
  }

  const getCurrentThreadTitle = id => userThreads.find(t => t.id === id)?.title || 'Без имени';

  const setThreadTitle = (id, title) => setUserThreads(prevThreads => prevThreads.map(t => {
    if (t.id === id) {
      return {...t, title};
    }
    return t;
  }));

  return {
    isUserThreadsLoading,
    userThreads,
    getThreadById,
    deleteThread,
    createThread,
    getCurrentThreadTitle,
    setThreadTitle
  };
}
