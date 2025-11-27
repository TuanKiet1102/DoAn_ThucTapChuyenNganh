document.addEventListener('DOMContentLoaded', () => {
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

  // Attach drag listeners to cards (initial load)
  function attachCardDragListeners() {
    document.querySelectorAll('.card').forEach(card => {
      // Avoid attaching twice
      if (card.dataset.dragAttached === '1') return;
      card.dataset.dragAttached = '1';

      card.addEventListener('dragstart', e => {
        dragData = {
          cardId: card.dataset.cardId,
          fromCol: card.dataset.colId,
          fromDate: card.dataset.date
        };
        e.dataTransfer.effectAllowed = 'move';
        try {
          e.dataTransfer.setData('text/plain', dragData.cardId);
        } catch (err) {
          // Some browsers may throw; it's non-fatal
        }
        setTimeout(() => card.classList.add('dragging'), 0);
      });

      card.addEventListener('dragend', () => {
        document.querySelectorAll('.cards').forEach(c => c.classList.remove('drag-over'));
        const el = document.querySelector(`[data-card-id="${dragData?.cardId}"]`);
        if (el) el.classList.remove('dragging');
        dragData = null;
      });
    });
  }

  attachCardDragListeners();

  // Attach drop listeners to containers
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
      const toDate = container.dataset.date || dragData.fromDate;
      const position = closestCardIndex(container, e.clientY);

      const params = new URLSearchParams({
        action: 'move_card',
        card_id: dragData.cardId,
        from_col: dragData.fromCol,
        to_col: toCol,
        position: String(position),
        from_date: dragData.fromDate,
        to_date: toDate
      });

      fetch(`index.php?date=${encodeURIComponent(toDate)}`, {
        method: 'POST',
        headers: { 'Content-Type': 'application/x-www-form-urlencoded; charset=UTF-8' },
        body: params
      })
        .then(res => res.json())
        .then(() => {
          // Reload to the target date so user sees the result
          const url = new URL(window.location.href);
          url.searchParams.set('date', toDate);
          window.location.replace(url.toString());
        })
        .catch(() => {
          window.location.reload();
        });
    });
  });

  // If your UI dynamically adds cards without full reload, call attachCardDragListeners() after insertion.
});

