/*// Lorsque le DOM est complètement chargé, on appelle la fonction fetchPokemons() pour récupérer la liste des Pokémon.
document.addEventListener("DOMContentLoaded", () => {
    fetchPokemons();
});

// Sélection des éléments du DOM pour les utiliser plus tard dans le code.
const search = document.querySelector("#search"); 
const errorMessage = document.querySelector(".error-message"); 
let allPokemons = []; 
const hearth = document.querySelector(".hearth"); 

//  pour la recherche de Pokémon
search.addEventListener('input', () => {
    const pokemonName = search.value.toLowerCase(); // valeur en minuscules
    let filteredPokemons = [...allPokemons]; // Copie de tous les Pokémon pour pouvoir les filtrer

    // Si la recherche est vide, on réaffiche tous les Pokémon et on cache le message d'erreur
    if (pokemonName === '') {
        displayPokemons(allPokemons); // Affichage de tous les Pokémon
        errorMessage.style.display = 'none'; 
    } else {
        // Si un nom est saisi, on filtre les Pokémon selon ce nom
        filteredPokemons = filteredPokemons.filter(pokemon =>
            pokemon.name.toLowerCase().includes(pokemonName) // Si le nom du Pokémon contient le texte recherché
        );

        // Si aucun Pokémon n'est trouvé, on affiche un message d'erreur
        if (filteredPokemons.length === 0) {
            displayPokemons([]); // Affichage d'une liste vide
            errorMessage.style.display = 'block';
            errorMessage.textContent = 'INTROUVABLE'; 
        } else {
            displayPokemons(filteredPokemons); // Affichage des Pokémon filtrés
            errorMessage.style.display = 'none'; 
        }
        console.log(filteredPokemons); //  console
    }
});

// Fonction pour récupérer les Pokémon depuis l'API
function fetchPokemons() {
    fetch(`https://pokeapi.co/api/v2/pokemon?limit=500`) // Appel à l'API Pokémon pour obtenir les 500 premiers Pokémon
    .then(response => response.json()) //  réponse en format JSON
    .then(data =>  {
        allPokemons = data.results; // Sauvegarde des résultats dans la variable 
        displayPokemons(allPokemons); // Affichage de tous les Pokémon
    });
}

// Fonction pour afficher les Pokémon dans le DOM
function displayPokemons(pokemons) {
    const pokemonCards = document.querySelector("#pokemonCards"); // Élément dans lequel afficher les cartes des Pokémon
    pokemonCards.innerHTML = ''; // Réinitialise le contenu pour éviter les doublons

    // Création d'un tableau de promesses pour récupérer les détails de chaque Pokémon 
    const pokemonPromises = pokemons.map(pokemon => 
        fetch (pokemon.url) // Requête pour récupérer les détails du Pokémon via son URL
            .then(response => response.json()) // Conversion de la réponse en JSON
            .then(pokemonData =>  {
                return {
                    name: pokemon.name,
                    image: pokemonData.sprites.other['official-artwork'].front_default, 
                    types: pokemonData.types.map(type => type.type.name).join(' ') 
                };
            })
        );
    
    // Traitement de toutes les promesses une fois qu'elles sont résolues
    Promise.all(pokemonPromises)
        .then(pokemonDataList => {
            // Création et ajout de chaque carte de Pokémon dans le DOM
            pokemonDataList.forEach(pokemonData => {
                const pokemonElement = document.createElement('div'); 
                pokemonElement.classList.add("pokemon"); 

                
                const pokemonImage = document.createElement("img");
                pokemonImage.classList.add("image-pokemon");
                pokemonImage.src = pokemonData.image;

                
                const hearth = document.createElement("img");
                hearth.classList.add("hearth");
                hearth.src = "Images/hearth1.png"; 

               
                const hearth2 = document.createElement("img");
                hearth2.classList.add("hearth2");
                hearth2.src = "Images/hearth2.png"; 

                
                hearth.addEventListener("click", () => {
                    hearth2.classList.toggle('opacity-yes'); 
                });

                
                const pokemonName = document.createElement('p');
                pokemonName.classList.add("pokemon-name");
                pokemonName.textContent = pokemonData.name;

                const pokemonTypes = document.createElement('p');
                pokemonTypes.classList.add("pokemon-types");
                pokemonTypes.textContent = pokemonData.types;

                // Ajout des éléments à l'élément principal de la carte Pokémon
                pokemonElement.appendChild(hearth);
                pokemonElement.appendChild(hearth2);
                pokemonElement.appendChild(pokemonImage);
                pokemonElement.appendChild(pokemonName);
                pokemonElement.appendChild(pokemonTypes);
                pokemonCards.appendChild(pokemonElement); 
            });
        })
        .catch(error => {
            console.error("Erreur ", error); 
        });
} */




/*

function callPokemons() {
    fetch(`https://pokeapi.co/api/v2/pokemon/1/`)
    .then (response => response.json())
    .then (data => console.log(data))

    
    
} 

callPokemons(); */

/*document.querySelectorAll('.filter-btn').forEach(btn => btn.addEventListener('click', () => 
    document.querySelectorAll('.pokemon-card').forEach(card => card.style.display = (btn.dataset.filter === 'all' || card.dataset.type === btn.dataset.filter) ? 'block' : 'none')
  )); */





  /*document.querySelectorAll('.filter-btn').forEach(btn =>  btn.addEventListener('click', () => 
    document.querySelectorAll('.pokemon-card').forEach(card => card.style.display = (btn.dataset.filter === 'all' || card.dataset.type === btn.dataset.filter) ? 'block' : 'none')));
  
  */







































  const search = document.querySelector('#search');
  const cardSection = document.querySelector('#pokemonCards');
  const hide = document.querySelector('.none');
  



  const pokemonsCount = 200;
  var pokemonsArray = {};

    document.addEventListener("DOMContentLoaded", async () => {
        
        

        for (let i = 1; i <= pokemonsCount; i++) {
            await fetching(i);
            

           let pokemonCards = document.createElement('div');
            pokemonCards.classList.add("pokemon");

            let pokemonName = document.createElement('p');
            pokemonName.classList.add("pokemon-name");
            pokemonName.innerText =  pokemonsArray[i]["name"].toLowerCase();

            let pokemonImg = document.createElement('img');
            pokemonImg.classList.add("image-pokemon");
            pokemonImg.src = pokemonsArray[i]["img"];

            let pokemonGeneration = document.createElement('p');
            pokemonGeneration.classList.add('pokemon-generation');
            pokemonGeneration.innerText = pokemonsArray[i]["generation"].toLowerCase()

            

            let typeDiv = document.createElement('div');
            typeDiv.classList.add('pokemon-types');

            let pokemonsType =  pokemonsArray[i]["types"];

            for (let i = 0; i < pokemonsType.length; i++) {
                let pokemonType = document.createElement('p');
                pokemonType.classList.add('pokemon-type');
                pokemonType.classList.add(pokemonsType[i]["type"]["name"]);
                pokemonType.innerText = pokemonsType[i]["type"]["name"];

                
                typeDiv.appendChild(pokemonType);
                
            }

            cardSection.appendChild(pokemonCards);
            pokemonCards.appendChild(pokemonImg);
            pokemonCards.appendChild(pokemonName);
            pokemonCards.appendChild(pokemonGeneration);
            pokemonCards.appendChild(typeDiv);


            //pour chercher
            

            search.addEventListener('input', () => {
                const type = search.value.toLowerCase();

                const names = document.querySelectorAll('.pokemon');
               

                names.forEach(name => {
                    const pokemons = name.querySelector('.pokemon-name');

                    if (type === '') {
                        name.classList.remove('none')
                    } else if (pokemons.textContent.toLowerCase().includes(type)) {
                        name.classList.remove('none')
                    } else {
                        name.classList.add('none')
                    }
                })


            });


            //filter type

            const selectPokemon = document.getElementById('pokemonSelect');
            const cards = document.querySelectorAll('.pokemon');
            const container = document.querySelectorAll('.pokemonCards');
            
            
            selectPokemon.addEventListener('change', () => {
                // console.log(cards.length)

                container.forEach( containe => {
                    containe.classList.remove('none'); 
                })

                const selectedType = document.getElementById('pokemonSelect').selectedOptions[0].value;

                cards.forEach(card => {
                    
                   const cardType = card.querySelector('.pokemon-type').textContent
                //    console.log('selectedType:', selectedType);
                //    console.log('cardType:', cardType);
                 //  console.log('ok')
                   
                    if (cardType === selectedType || selectedType === '' ) {
                        card.classList.remove('none')
                    } else {
                        card.classList.add('none')
                    }
                })

            })

            // filter generation

            const selectGeneration = document.getElementById('generation-filter');
            const cards2 = document.querySelectorAll('.pokemon');
            const container2 = document.querySelectorAll('.pokemonCards');

            selectGeneration.addEventListener('change', () => {
                

                container2.forEach( containe => {
                    containe.classList.remove('none'); 
                })

                const selectedType2 = document.getElementById('generation-filter').selectedOptions[0].value;

                cards2.forEach(card => {
                    
                   const cardType2 = card.querySelector('.pokemon-generation').textContent
              
                   
                    if (cardType2 === selectedType2 || selectedType2 === '' ) {
                        card.classList.remove('none')
                    } else {
                        card.classList.add('none')
                    }
                })

            })
            

            

               
                    
                    /*if (type === "") {
                        console.log('non');
                        pokemonCards.classList.remove('none');
                    }
            
                    if (type === pokemonName) {
                        console.log("its pikachu");
                        pokemonCards.classList.add('none');
                    } */
                    
            
    
            //});


            //

            /*search.addEventListener('input', () => {
                const type = search.value.toLowerCase(); // Récupère la valeur de recherche en minuscules
            
                const cards = document.querySelectorAll('.pokemon'); // Sélectionne toutes les cartes
            
                cards.forEach(card => {
                    const name = card.querySelector('.pokemon-name'); // Récupère le nom de chaque carte
            
                    // Si la recherche est vide, on affiche toutes les cartes
                    if (type === "") {
                        card.classList.remove('none');
                    } else if (name.textContent.toLowerCase().includes(type)) {
                        // Si le nom contient le texte recherché, on affiche la carte
                        card.classList.remove('none');
                    } else {
                        // Sinon, on cache la carte
                        card.classList.add('none');
                    }
                });
            }); */
            
        }

        
    });


    async function fetching(num) {

       

            let url =  'index.php' + num.toString();
            

            

            let response = await fetch (url);
            const data = await response.json();

            

            

            console.log(data);

            let pokemonName = data["name"];
            let pokemonType = data["types"];
            let pokemonImg = data["sprites"]["other"]["official-artwork"]["front_default"];
            
            let speciesUrl = data["species"]["url"];
            let speciesResponse = await fetch(speciesUrl);
            let speciesData = await speciesResponse.json();
        
            // Récupérer la génération
            let pokemonGeneration = speciesData["generation"]["name"];
        
            console.log(pokemonGeneration); 

            
            //console.log(pokemonName);

            pokemonsArray[num] = { 
                "name" : pokemonName, 
                "types" : pokemonType, 
                "img" : pokemonImg, 
                "generation" : pokemonGeneration
            }

        
        
    }
    
    
   








































