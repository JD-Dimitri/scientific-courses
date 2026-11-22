import { useState } from 'react';
import { Link, useParams } from 'react-router-dom';
import { api } from '../api.js';
import AuthLayout, { PasswordField } from '../components/AuthLayout.jsx';

const MIN_LENGTH = 10;

export default function ResetPassword() {
  const { token } = useParams();
  const [password, setPassword] = useState('');
  const [confirm, setConfirm] = useState('');
  const [errors, setErrors] = useState({});
  const [error, setError] = useState('');
  const [done, setDone] = useState('');
  const [sending, setSending] = useState(false);

  async function handleSubmit(event) {
    event.preventDefault();
    setError('');
    const local = {};
    if (password.length < MIN_LENGTH) local.password = `Au moins ${MIN_LENGTH} caractères.`;
    if (password !== confirm) local.confirm = 'Les deux mots de passe ne sont pas identiques.';
    setErrors(local);
    if (Object.keys(local).length) return;

    setSending(true);
    try {
      const data = await api('/reset-password/reset', { method: 'POST', body: { token, password } });
      setDone(data.message);
    } catch (err) {
      setErrors(err.errors || {});
      setError(err.message);
    } finally {
      setSending(false);
    }
  }

  return (
    <AuthLayout title="Nouveau mot de passe">
      {done ? (
        <>
          <p className="form-success" role="status">{done}</p>
          <p className="auth-links"><Link to="/connexion">Se connecter</Link></p>
        </>
      ) : (
        <>
          <form onSubmit={handleSubmit} noValidate>
            <PasswordField id="password" label={`Nouveau mot de passe (${MIN_LENGTH} caractères minimum)`} autoComplete="new-password" required value={password} onChange={(e) => setPassword(e.target.value)} error={errors.password} />
            <PasswordField id="confirm" label="Confirme le nouveau mot de passe" autoComplete="new-password" required value={confirm} onChange={(e) => setConfirm(e.target.value)} error={errors.confirm} />
            {error && <p className="form-error" role="alert">{error}</p>}
            <button type="submit" disabled={sending}>{sending ? 'Enregistrement…' : 'Enregistrer le mot de passe'}</button>
          </form>
          <p className="auth-links"><Link to="/mot-de-passe-oublie">Demander un nouveau lien</Link></p>
        </>
      )}
    </AuthLayout>
  );
}
