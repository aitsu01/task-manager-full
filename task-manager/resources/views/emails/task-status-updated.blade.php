<h2>Aggiornamento Task</h2>

<p>Il task <strong>{{ $task->title }}</strong> è stato aggiornato.</p>

<p>Stato precedente: {{ $oldStatus }}</p>
<p>Nuovo stato: {{ $task->status }}</p>

<p>Progetto: {{ $task->project->name }}</p>