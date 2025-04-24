const search = document.querySelector('#search');
const cardSection = document.querySelector('#pokemonCards');
const errorMsg = document.getElementsByClassName('error-message');

// Charge les pokémons 
async function loadPokemons() {
    const pokemons = [];
    const ids = [];

    for (let i = 1; i <= 20; i++) {
        ids.push(i);
    }

    const promises = ids.map(id => {
        return fetch(`https://pokeapi.co/api/v2/pokemon/${id}`)
            .then(res => res.json())
            .then(async data => {
                try {
                    console.log(data);
                    const speciesRes = await fetch(data.species.url);
                    const speciesData = await speciesRes.json();
                    const generation = speciesData.generation.name;

                    return { ...data, generation };
                } catch (error) {
                    console.error("Erreur : ", error);
                }
            })
            .catch(error => {
                console.error("Erreur : ", error);
                return null;
            });
    });

    const results = await Promise.all(promises);

    results.forEach(p => {
        // Créer la carte
        const card = document.createElement('div');
        card.classList.add('pokemon');
        card.innerHTML = `
            <img class="hearth" src="Images/hearth1.png">
            <img class="hearth2" src="Images/hearth2.png">
            <img class="image-pokemon" src="${p.sprites.other['official-artwork'].front_default}" alt="${p.name}">
            <p class="pokemon-name">${p.name}</p>
            <p class="pokemon-generation">${p.generation}</p>
            <div class="pokemon-types">${p.types.map(t => `<p class="pokemon-type ${t.type.name}">${t.type.name}</p>`).join('  ')}</div>
        `;
    
        // Ajouter un événement click
        card.onclick = () => {
            localStorage.setItem('poke', JSON.stringify(p));
            location.href = 'cards2.html';
        };
    
        // Ajouter la carte sur la page
        document.querySelector('#pokemonCards').appendChild(card);
    });
    
    
    

    // Coeurs de like
    const hearths = document.querySelectorAll('.hearth2');
    hearths.forEach(hearth => {
        hearth.addEventListener('click', (e) => {
            e.stopPropagation(); // éviter de déclencher le clic sur la carte
            hearth.classList.toggle('opacity');
        });
    });
}

// Template pour la carte
const createCard = ({ name, sprites, types, generation }) => {
    return `
        <div class="pokemon">
            <img class="hearth" src="Images/hearth1.png">
            <img class="hearth2" src="Images/hearth2.png">
            <p class="pokemon-name">${name}</p>
            <img class="image-pokemon" src="${sprites.other["official-artwork"].front_default}" alt="${name}">
            <div class="pokemon-types">
                ${types.map(t => `<p class="pokemon-type ${t.type.name}">${t.type.name}</p>`).join('')}
            </div>
            <p class="pokemon-generation">${generation}</p>
        </div>
    `;
};

// Initialisation
document.addEventListener("DOMContentLoaded", async () => {
    loadPokemons();

    const selectPokemon = document.getElementById('pokemonSelect');
    const selectGeneration = document.getElementById('generation-filter');

    function filterPokemons() {
        const term = search.value.toLowerCase();
        const selectedType = selectPokemon.value.toLowerCase();
        const selectedGeneration = selectGeneration.value.toLowerCase();

        document.querySelectorAll('.pokemon').forEach(card => {
            const name = card.querySelector('.pokemon-name').textContent.toLowerCase();
            const types = Array.from(card.querySelectorAll('.pokemon-type')).map(t => t.textContent.toLowerCase());
            const generation = card.querySelector('.pokemon-generation').textContent.toLowerCase();

            const matchName = name.includes(term);
            const matchType = selectedType === '' || types.includes(selectedType);
            const matchGen = selectedGeneration === '' || generation === selectedGeneration;

            if (matchName && matchType && matchGen) {
                card.style.display = 'flex';
            } else {
                card.style.display = 'none';
            }
        });

        const cardVisible = Array.from(document.querySelectorAll('.pokemon')).filter(card => card.style.display === 'flex');

        if (cardVisible.length === 0) {
            document.querySelector('#errorMsg').style.display = 'flex';
        } else {
            document.querySelector('#errorMsg').style.display = 'none';
        }
    }

    search.addEventListener('input', filterPokemons);
    selectPokemon.addEventListener('change', filterPokemons);
    selectGeneration.addEventListener('change', filterPokemons);
});
