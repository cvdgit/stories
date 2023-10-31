import React, {useMemo} from "react";
import PropTypes from 'prop-types'
import "./Tooltip.scss";

export function Tooltip({text, className, children, position}) {
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

Tooltip.defaultProps = {
  position: 'top'
}

Tooltip.propTypes = {
  position: PropTypes.string,
}
