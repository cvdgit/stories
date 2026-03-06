import React, {useMemo} from "react";
import "./Tooltip.scss";

export function Tooltip({text, className, children, position = 'top'}) {
  const memoizedChildren = useMemo(() => children, [children]);
  return (
    <div className={`tooltip ${className}`}>
      {memoizedChildren}
      <div className={`tooltip-container ${position}`}>
        <div className="tooltip-inner">
          {text}
        </div>
      </div>
    </div>
  );
}
