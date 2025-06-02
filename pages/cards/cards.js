const search = document.querySelector('#search');
const cardSection = document.querySelector('#pokemonCards');
const errorMsg = document.getElementsByClassName('error-message');

// Charge les pokémons 
async function loadPokemons() {
    const ids = [];

    for (let i = 1; i <= 50; i++) {
        ids.push(i);
    }

    const promises = ids.map(id => {
        return fetch(`https://pokeapi.co/api/v2/pokemon/${id}`)
            .then(res => res.json())
            .then(async data => {
                try {
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
 

    results.forEach((p, index) => {
        

        // je crée la carte
        const card = document.createElement('div');
        card.classList.add('pokemon');
        card.innerHTML = `
            <img class="hearth" src="/Images/hearth1.png">
            <img class="hearth2" src="/Images/hearth2.png">
            <img class="image-pokemon" src="${p.sprites.other['official-artwork'].front_default}" alt="${p.name}">
            <p class="pokemon-name">${p.name}</p>
            <p class="pokemon-generation">${p.generation}</p>
            <div class="pokemon-types">${p.types.map(t => `<p class="pokemon-type ${t.type.name}">${t.type.name}</p>`).join('  ')}</div>
        `;

        // si je click ca envoi vers autre page avec detailes
        card.onclick = () => {
            localStorage.setItem('poke', JSON.stringify(p));
            location.href = 'cards2.html';
        };

        // viseul du like
        const hearth = card.querySelector('.hearth2');
        const liked = localStorage.getItem(`like_${index}`) === "true";
        if (liked) {
            hearth.classList.add('opacity');
        }

        // gerer like et stocker en local storage
        hearth.addEventListener('click', (e) => {
            e.stopPropagation();
            hearth.classList.toggle('opacity');
            const isLiked = hearth.classList.contains('opacity');
            localStorage.setItem(`like_${index}`, isLiked ? "true" : "false");

            // deplace les likes
            if (isLiked) {
                cardSection.prepend(card); //met au tout debut, si c bon
            } else {
                cardSection.appendChild(card); //mettre a la fin la carte (faire fixe)
            }
        });

        // Ajouter la carte au bon endroit
        if (liked) {
            cardSection.prepend(card); //met au tout debut, c bon
        } else {
            cardSection.appendChild(card); //mettre a la fin la carte (faire fixe)
        }

        
    });
}

// apres si bien charger la page 
document.addEventListener("DOMContentLoaded", async () => {
    loadPokemons(); //affiche pokemons

    const selectPokemon = document.getElementById('pokemonSelect');
    const selectGeneration = document.getElementById('generation-filter');

    function filterPokemons() { //filtre pokemons
        const term = search.value.toLowerCase();
        const selectedType = selectPokemon.value.toLowerCase();
        const selectedGeneration = selectGeneration.value.toLowerCase();

        document.querySelectorAll('.pokemon').forEach(card => {
            const name = card.querySelector('.pokemon-name').textContent.toLowerCase();
            const types = Array.from(card.querySelectorAll('.pokemon-type')).map(t => t.textContent.toLowerCase()); //selectionne des types de arrau
            const generation = card.querySelector('.pokemon-generation').textContent.toLowerCase();

            const matchName = name.includes(term); //on stock ici ce que lon avait mit le nom de la carte dans la recherche
            const matchType = selectedType === '' || types.includes(selectedType); //et types
            const matchGen = selectedGeneration === '' || generation === selectedGeneration; // ainsi la generation

            if (matchName && matchType && matchGen) { //si prend en compte toutes les troics, alors on affiche, sinon disparait
                card.style.display = 'flex';
            } else {
                card.style.display = 'none';
            }
        });

        const cardVisible = Array.from(document.querySelectorAll('.pokemon')).filter(card => card.style.display === 'flex');
        // si aucune pokemon afficher alors on afficher aucun trouver sinon disparait ereur
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
