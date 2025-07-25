import React, {useState} from "react";
import PromptsDialog from "./PromptsDialog";
import PromptDialog from "./PromptDialog";

export default function Prompts({promptsDialogOpen, setPromptsDialogOpen}) {
  const [promptDialogOpen, setPromptDialogOpen] = useState(false);
  const [loadPrompts, setLoadPrompts] = useState(true);
  const [currentPrompt, setCurrentPrompt] = useState(null);

  return (
    <>
      <PromptsDialog
        open={promptsDialogOpen}
        hideDialog={() => setPromptsDialogOpen(false)}
        showPromptDialog={(prompt) => {
          setCurrentPrompt(prompt)
          setPromptDialogOpen(true)
        }}
        loadPrompts={loadPrompts}
        promptsLoaded={() => setLoadPrompts(false)}
      />
      <PromptDialog
        open={promptDialogOpen}
        hideDialog={() => {
          setPromptDialogOpen(false)
          setCurrentPrompt(null)
        }}
        saveHandler={() => {
          setLoadPrompts(true)
        }}
        currentPrompt={currentPrompt}
      />
    </>
  )
}
