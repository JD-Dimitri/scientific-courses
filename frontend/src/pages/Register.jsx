import { useState } from 'react';
import { Link, Navigate, useNavigate } from 'react-router-dom';
import { useAuth } from '../auth.jsx';
import AuthLayout, { Field } from '../components/AuthLayout.jsx';

const MIN_LENGTH = 10;

export default function Register() {
  const { user, register } = useAuth();
  const navigate = useNavigate();
  const [form, setForm] = useState({ firstName: '', email: '', password: '', confirm: '' });
  const [errors, setErrors] = useState({});
  const [error, setError] = useState('');
  const [sending, setSending] = useState(false);

  if (user) return <Navigate to="/" replace />;

  const update = (field) => (event) => setForm({ ...form, [field]: event.target.value });

  async function handleSubmit(event) {
    event.preventDefault();
    setError('');
    const local = {};
    if (form.password.length < MIN_LENGTH) local.password = `Au moins ${MIN_LENGTH} caractères.`;
    if (form.password !== form.confirm) local.confirm = 'Les deux mots de passe ne sont pas identiques.';
    setErrors(local);
    if (Object.keys(local).length) return;

    setSending(true);
    try {
      await register(form.firstName, form.email, form.password);
      navigate('/', { replace: true });
    } catch (err) {
      setErrors(err.errors || {});
      if (!err.errors || !Object.keys(err.errors).length) setError(err.message);
    } finally {
      setSending(false);
    }
  }

  return (
    <AuthLayout title="Créer un compte élève" intro="Ton compte enregistre tes réponses et ta progression dans chaque chapitre.">
      <form onSubmit={handleSubmit} noValidate>
        <Field id="firstName" label="Prénom" autoComplete="given-name" required value={form.firstName} onChange={update('firstName')} error={errors.firstName} />
        <Field id="email" label="Adresse e-mail" type="email" autoComplete="email" required value={form.email} onChange={update('email')} error={errors.email} />
        <Field id="password" label={`Mot de passe (${MIN_LENGTH} caractères minimum)`} type="password" autoComplete="new-password" required value={form.password} onChange={update('password')} error={errors.password} />
        <Field id="confirm" label="Confirme le mot de passe" type="password" autoComplete="new-password" required value={form.confirm} onChange={update('confirm')} error={errors.confirm} />
        {error && <p className="form-error" role="alert">{error}</p>}
        <button type="submit" disabled={sending}>{sending ? 'Création…' : 'Créer mon compte'}</button>
      </form>
      <p className="auth-links">
        <Link to="/connexion">J'ai déjà un compte</Link>
      </p>
    </AuthLayout>
  );
}
