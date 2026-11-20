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

export function Field({ id, label, error, ...inputProps }) {
  return (
    <div className="field">
      <label htmlFor={id}>{label}</label>
      <input id={id} name={id} aria-invalid={Boolean(error)} aria-describedby={error ? `${id}-error` : undefined} {...inputProps} />
      {error && <p className="field-error" id={`${id}-error`}>{error}</p>}
    </div>
  );
}
