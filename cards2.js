document.addEventListener("DOMContentLoaded", () => {
    const p = JSON.parse(localStorage.getItem('poke'));

    // si le Pokémon existe on l'affiche
    if (p) {
        const container = document.getElementById("pokemonDetails");

        container.innerHTML = `
        <div class="block-stats">
            <div class="stat">
            <h2>${p.name}</h2>
           
            <p>Génération: ${p.generation}</p>
            <p>Types: ${p.types.map(t => t.type.name).join(', ')}</p>
            <p>HP: ${p.stats.find(s => s.stat.name === 'hp').base_stat}</p>
            <p>Attaque: ${p.stats.find(s => s.stat.name === 'attack').base_stat}</p>
            <button class="btn-back" onclick="window.history.back()">⬅ Retour</button>
            </div>
             <img src="${p.sprites.other['official-artwork'].front_default}" alt="${p.name}">
        </div>
            `;
    }
});
