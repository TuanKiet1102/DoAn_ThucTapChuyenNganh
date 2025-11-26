function closestCardIndex(container, clientY) {
  const cards = Array.from(container.querySelectorAll('.card'));
  let index = cards.length;
  for (let i = 0; i < cards.length; i++) {
    const rect = cards[i].getBoundingClientRect();
    if (clientY < rect.top + rect.height / 2) {
      index = i;
      break;
    }
  }
  return index;
}

let dragData = null;

document.querySelectorAll('.card').forEach(card => {
  card.addEventListener('dragstart', e => {
    dragData = {
      cardId: card.dataset.cardId,
      fromCol: card.dataset.colId
    };
    e.dataTransfer.effectAllowed = 'move';
    e.dataTransfer.setData('text/plain', dragData.cardId);
    setTimeout(() => card.classList.add('dragging'), 0);
  });

  card.addEventListener('dragend', () => {
    document.querySelectorAll('.cards').forEach(c => c.classList.remove('drag-over'));
    const el = document.querySelector(`[data-card-id="${dragData.cardId}"]`);
    if (el) el.classList.remove('dragging');
    dragData = null;
  });
});

document.querySelectorAll('.cards').forEach(container => {
  container.addEventListener('dragover', e => {
    e.preventDefault();
    container.classList.add('drag-over');
  });

  container.addEventListener('dragleave', () => {
    container.classList.remove('drag-over');
  });

  container.addEventListener('drop', e => {
    e.preventDefault();
    container.classList.remove('drag-over');
    if (!dragData) return;

    const toCol = container.dataset.colId;
    const position = closestCardIndex(container, e.clientY);

    fetch('index.php', {
      method: 'POST',
      headers: { 'Content-Type': 'application/x-www-form-urlencoded; charset=UTF-8' },
      body: new URLSearchParams({
        action: 'move_card',
        card_id: dragData.cardId,
        from_col: dragData.fromCol,
        to_col: toCol,
        position: position
      })
    }).then(res => res.json())
      .then(() => location.reload());
  });
});
