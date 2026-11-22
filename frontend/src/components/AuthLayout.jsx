import { useState } from 'react';

/** Mise en page commune aux écrans de connexion, d'inscription et de mot de passe. */
export default function AuthLayout({ title, intro, children }) {
  return (
    <main className="auth">
      <div className="eyebrow">Scientific Courses</div>
      <h1>{title}</h1>
      {intro && <p className="auth-intro">{intro}</p>}
      {children}
    </main>
  );
}

function EyeIcon({ crossed }) {
  return (
    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="2" strokeLinecap="round" strokeLinejoin="round" aria-hidden="true">
      <path d="M2 12s3.6-7 10-7 10 7 10 7-3.6 7-10 7S2 12 2 12z" />
      <circle cx="12" cy="12" r="3" />
      {crossed && <line x1="3" y1="3" x2="21" y2="21" />}
    </svg>
  );
}

/** Champ de mot de passe avec un bouton œil pour afficher ou masquer ce qu'on tape. */
export function PasswordField({ id, label, error, ...inputProps }) {
  const [visible, setVisible] = useState(false);
  return (
    <div className="field">
      <label htmlFor={id}>{label}</label>
      <div className="password-wrap">
        <input
          id={id}
          name={id}
          type={visible ? 'text' : 'password'}
          autoCapitalize="none"
          spellCheck={false}
          aria-invalid={Boolean(error)}
          aria-describedby={error ? `${id}-error` : undefined}
          {...inputProps}
        />
        <button
          type="button"
          className="eye"
          onClick={() => setVisible(!visible)}
          aria-label={visible ? 'Masquer le mot de passe' : 'Afficher le mot de passe'}
          aria-pressed={visible}
          aria-controls={id}
          title={visible ? 'Masquer le mot de passe' : 'Afficher le mot de passe'}
        >
          <EyeIcon crossed={visible} />
        </button>
      </div>
      {error && <p className="field-error" id={`${id}-error`}>{error}</p>}
    </div>
  );
}

export function Field({ id, label, error, ...inputProps }) {
  return (
    <div className="field">
      <label htmlFor={id}>{label}</label>
      <input id={id} name={id} aria-invalid={Boolean(error)} aria-describedby={error ? `${id}-error` : undefined} {...inputProps} />
      {error && <p className="field-error" id={`${id}-error`}>{error}</p>}
    </div>
  );
}
