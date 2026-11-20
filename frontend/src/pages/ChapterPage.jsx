import { useCallback, useEffect, useState } from 'react';
import { Link, useParams } from 'react-router-dom';
import { api } from '../api.js';
import MathHtml from '../components/MathHtml.jsx';
import QuestionItem from '../components/QuestionItem.jsx';
import TopBar from '../components/TopBar.jsx';

function countFor(questions, attempts) {
  let answered = 0;
  let correct = 0;
  for (const q of questions) {
    const value = attempts[q.id];
    if (value !== undefined) {
      answered += 1;
      if (value) correct += 1;
    }
  }
  return { answered, correct, total: questions.length };
}

function examMessage({ answered, correct, total }) {
  if (answered === 0) return 'Aucune question corrigée pour l’instant.';
  if (answered < total) return `${answered} questions corrigées sur ${total}.`;
  if (correct >= 14) return 'Chapitre acquis. Tu peux passer au suivant.';
  if (correct >= 10) return 'Presque : reprends les sous-chapitres où tu as perdu des points.';
  return 'Refais les sous-chapitres concernés avant de repasser l’examen.';
}

export default function ChapterPage() {
  const { slug } = useParams();
  const [chapter, setChapter] = useState(null);
  const [attempts, setAttempts] = useState({});
  const [error, setError] = useState('');
  const [saveError, setSaveError] = useState('');

  useEffect(() => {
    setChapter(null);
    setError('');
    api(`/chapters/${encodeURIComponent(slug)}`)
      .then((data) => {
        setChapter(data.chapter);
        setAttempts(data.attempts || {});
      })
      .catch((err) => setError(err.message));
  }, [slug]);

  // Mise à jour immédiate à l'écran, puis enregistrement. En cas d'échec, on revient en arrière.
  const handleMark = useCallback(async (questionId, value) => {
    setSaveError('');
    const previous = attempts[questionId];
    setAttempts((current) => {
      const next = { ...current };
      if (value === null) delete next[questionId];
      else next[questionId] = value;
      return next;
    });
    try {
      if (value === null) await api(`/questions/${questionId}/attempt`, { method: 'DELETE' });
      else await api(`/questions/${questionId}/attempt`, { method: 'PUT', body: { correct: value } });
    } catch (err) {
      setSaveError(`Réponse non enregistrée : ${err.message}`);
      setAttempts((current) => {
        const next = { ...current };
        if (previous === undefined) delete next[questionId];
        else next[questionId] = previous;
        return next;
      });
    }
  }, [attempts]);

  if (error) {
    return (
      <>
        <TopBar />
        <main className="page">
          <p className="form-error" role="alert">{error}</p>
          <Link to="/">Retour au tableau de bord</Link>
        </main>
      </>
    );
  }

  if (!chapter) {
    return (
      <>
        <TopBar />
        <main className="page"><p className="loading">Chargement du chapitre…</p></main>
      </>
    );
  }

  const exam = countFor(chapter.exam, attempts);

  return (
    <>
      <TopBar />
      <nav className="subnav" aria-label="Sous-chapitres">
        <div>
          {chapter.sections.map((section) => {
            const c = countFor(section.questions, attempts);
            return (
              <a key={section.slug} href={`#${section.slug}`}>
                {section.number} {section.title}
                {c.answered > 0 && <b>{c.correct}/{c.answered}</b>}
              </a>
            );
          })}
          <a href="#examen">Examen{exam.answered > 0 && <b>{exam.correct}/{exam.answered}</b>}</a>
        </div>
      </nav>

      <main className="page cahier">
        <header>
          <div className="eyebrow">
            <Link to="/">{chapter.subject.name}</Link> · Chapitre {chapter.number}
            {chapter.cnamCode && <span className="cnam">{chapter.cnamCode}</span>}
          </div>
          <h1>{chapter.title}</h1>
          {chapter.introHtml && <MathHtml html={chapter.introHtml} />}
          <div className="bord">
            {chapter.sections.map((section) => {
              const c = countFor(section.questions, attempts);
              return (
                <a key={section.slug} href={`#${section.slug}`}>
                  <small>{section.number}</small>
                  <strong>{c.correct}/{c.total}</strong>
                  <i><b style={{ width: `${c.total ? (c.answered / c.total) * 100 : 0}%` }} /></i>
                </a>
              );
            })}
            <a href="#examen">
              <small>Examen</small>
              <strong>{exam.correct}/{exam.total}</strong>
              <i><b style={{ width: `${exam.total ? (exam.answered / exam.total) * 100 : 0}%` }} /></i>
            </a>
          </div>
        </header>

        {saveError && <p className="form-error sticky-error" role="alert">{saveError}</p>}

        {chapter.sections.map((section) => (
          <section key={section.slug} id={section.slug}>
            <div className="num">Sous-chapitre {section.number}</div>
            <h2>{section.title}</h2>
            <h3>Cours</h3>
            <MathHtml html={section.courseHtml} className="course" />

            {section.examples.length > 0 && <h3>Exemples corrigés</h3>}
            {section.examples.map((example) => (
              <div key={example.title} className="ex">
                <h4>{example.title}</h4>
                <MathHtml html={example.statementHtml} />
                <MathHtml html={example.solutionHtml} className="sol" />
              </div>
            ))}

            <h3>{section.questions.length} questions corrigées</h3>
            <ol className="qs">
              {section.questions.map((question, i) => (
                <QuestionItem key={question.id} question={question} number={i + 1} attempt={attempts[question.id]} onMark={handleMark} />
              ))}
            </ol>
          </section>
        ))}

        <section id="examen" className="exam">
          <div className="num">Fin du chapitre {chapter.number}</div>
          <h2>Examen : {chapter.exam.length} questions</h2>
          <p>Fais l'examen en une fois, sans le cours. Écris toutes tes réponses sur papier avant d'ouvrir la moindre correction, puis corrige-toi question par question. Repère : à partir de 14/20, le chapitre est acquis.</p>
          <div className="score" aria-live="polite">
            <strong>{exam.correct} / {exam.total}</strong>
            <span>{examMessage(exam)}</span>
          </div>
          <ol className="qs">
            {chapter.exam.map((question, i) => (
              <QuestionItem key={question.id} question={question} number={i + 1} attempt={attempts[question.id]} onMark={handleMark} />
            ))}
          </ol>
        </section>
      </main>
    </>
  );
}
