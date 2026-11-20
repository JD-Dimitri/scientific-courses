import { useState } from 'react';
import MathHtml from './MathHtml.jsx';

const LEVELS = { 1: 'Facile', 2: 'Moyen', 3: 'Difficile' };

/**
 * Une question : énoncé, correction à dévoiler, puis auto-évaluation « juste » ou « faux ».
 * attempt vaut true, false ou undefined (pas encore évaluée).
 */
export default function QuestionItem({ question, number, attempt, onMark }) {
  const [open, setOpen] = useState(attempt !== undefined);

  const status = attempt === true ? 'done-ok' : attempt === false ? 'done-ko' : '';
  const toggle = (value) => onMark(question.id, attempt === value ? null : value);

  return (
    <li className={`question ${status}`}>
      <span className="question-number">{number}</span>
      <div className="question-body">
        <div className="q">
          <span className={`lvl d${question.level}`}>{LEVELS[question.level]}</span>
          <MathHtml html={question.statementHtml} as="span" />
        </div>
        {open && <MathHtml html={question.correctionHtml} className="r show" />}
        <div className="acts">
          <button type="button" className="ghost" onClick={() => setOpen(!open)}>
            {open ? 'Masquer la correction' : 'Voir la correction'}
          </button>
          {open && (
            <>
              <button type="button" className="ok" aria-pressed={attempt === true} onClick={() => toggle(true)}>
                J'avais juste
              </button>
              <button type="button" className="ko" aria-pressed={attempt === false} onClick={() => toggle(false)}>
                J'avais faux
              </button>
            </>
          )}
        </div>
      </div>
    </li>
  );
}
