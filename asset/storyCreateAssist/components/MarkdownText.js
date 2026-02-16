import React from "react";
import ReactMarkdown from "react-markdown";
import remarkGfm from "remark-gfm";
import remarkMath from "remark-math";

const defaultComponents = {
  h1: 'h2',
  h2: 'h3',
  h3: 'h4',
}

const MarkdownTextImpl = ({children}) => {
  return (
    <div className="markdown-content">
      <ReactMarkdown remarkPlugins={[remarkGfm, remarkMath]} components={defaultComponents}>
        {children}
      </ReactMarkdown>
    </div>
  );
};

export const MarkdownText = React.memo(MarkdownTextImpl);
