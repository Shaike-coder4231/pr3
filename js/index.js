$(document).ready(function () {
    const container = $('#roomsContainer');
    const empty     = $('#roomsEmpty');

    function escapeHtml(s) {
        return String(s).replace(/[&<>"']/g, c => ({
            '&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;',"'":'&#39;'
        }[c]));
    }

    function renderRoom(room) {
        const features = (room.features || [])
            .map(f => `<li class="list-group-item">${escapeHtml(f)}</li>`)
            .join('');

        return `
            <div class="card">
                <img src="${escapeHtml(room.image_url)}" class="card-img-top" alt="${escapeHtml(room.name)}">
                <div
