document.addEventListener("DOMContentLoaded", () => {
    fetchPokemons();
});

const search = document.querySelector("#search");
let error = null;

search.addEventListener('input', () => {
    const pokemonName = search.value.toLowerCase();
    fetchPokemons(pokemonName);
});

function fetchPokemons(pokemonName = '') {
    fetch(`https://pokeapi.co/api/v2/pokemon?limit=100`)
        .then(response => response.json())
        .then(data => {
            const pokemonCard = document.querySelector("#pokemonCard");
            pokemonCard.innerHTML = '';

            let filteredPokemons = data.results;

            if (pokemonName !== '') {
                filteredPokemons = filteredPokemons.filter(pokemon =>
                    pokemon.name.toLowerCase().includes(pokemonName.toLowerCase())
                );
            }

            if (filteredPokemons.length === 0) {
                if (!error) {
                    error = document.createElement("div");
                    error.classList.add("error-message");
                    error.textContent = "Aucun Pokémon trouvé";
                    pokemonCard.appendChild(error);
                }
            } else {
                if (error) {
                    error = null;
                }

                filteredPokemons.forEach(pokemon => {
                    const pokemonElement = document.createElement('div');
                    pokemonElement.classList.add("pokemon");

                    const pokemonName = document.createElement('p');
                    pokemonName.classList.add("pokemon-name");
                    pokemonName.textContent = pokemon.name;

                    const pokemonImage = document.createElement("img");
                    pokemonImage.classList.add("image-pokemon");
                    fetch(pokemon.url)
                        .then(response => response.json())
                        .then(pokemonData => {
                            pokemonImage.src = pokemonData.sprites.other['official-artwork'].front_default;
                        });

                    pokemonElement.appendChild(pokemonName);
                    pokemonElement.appendChild(pokemonImage);
                    pokemonCard.appendChild(pokemonElement);
                });
            }
        });
}
