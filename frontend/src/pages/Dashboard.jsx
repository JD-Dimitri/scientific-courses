import { useEffect, useState } from 'react';
import { Link } from 'react-router-dom';
import { api } from '../api.js';
import { useAuth } from '../auth.jsx';
import TopBar from '../components/TopBar.jsx';

export default function Dashboard() {
  const { user } = useAuth();
  const [subjects, setSubjects] = useState(null);
  const [error, setError] = useState('');

  useEffect(() => {
    api('/subjects')
      .then((data) => setSubjects(data.subjects))
      .catch((err) => setError(err.message));
  }, []);

  return (
    <>
      <TopBar />
      <main className="page">
        <div className="eyebrow">Tableau de bord</div>
        <h1>Bonjour {user.firstName}</h1>
        <p>Chaque chapitre contient un cours par sous-chapitre, des exemples corrigés, 20 questions par sous-chapitre et un examen de 20 questions. La barre verte montre les questions réussies.</p>

        {error && <p className="form-error" role="alert">{error}</p>}
        {!subjects && !error && <p className="loading">Chargement des cours…</p>}

        {subjects && subjects.length === 0 && (
          <div className="empty">
            <p><strong>Aucun cours importé pour l'instant.</strong></p>
            <p>Dans le dossier backend, lance : <code>php bin/console app:import-chapter ../data/maths/01-calcul-scientifique.json</code></p>
          </div>
        )}

        {subjects?.map((subject) => (
          <section key={subject.slug} className="subject">
            <h2>{subject.name}</h2>
            <ul className="chapters">
              {subject.chapters.map((chapter) => {
                const pctCorrect = chapter.questionCount ? Math.round((chapter.correct / chapter.questionCount) * 100) : 0;
                const pctAnswered = chapter.questionCount ? Math.round((chapter.answered / chapter.questionCount) * 100) : 0;
                return (
                  <li key={chapter.slug}>
                    <Link to={`/chapitres/${chapter.slug}`}>
                      <span className="chapter-number">{String(chapter.number).padStart(2, '0')}</span>
                      <span className="chapter-title">
                        {chapter.title}
                        {chapter.cnamCode && <span className="cnam">{chapter.cnamCode}</span>}
                      </span>
                      <span className="chapter-score">{chapter.correct}/{chapter.questionCount}</span>
                      <span className="bar" aria-label={`${chapter.answered} questions faites sur ${chapter.questionCount}, ${chapter.correct} justes`}>
                        <i style={{ width: `${pctAnswered}%` }} />
                        <b style={{ width: `${pctCorrect}%` }} />
                      </span>
                    </Link>
                  </li>
                );
              })}
            </ul>
          </section>
        ))}
      </main>
    </>
  );
}
