# Pokebox v2

Pokebox v2 is an improved and more robust version of the original Pokebox project, enhanced with new frontend and backend features. This version introduces a cleaner structure, dynamic JavaScript interactions, and a PHP backend to manage users, cards, exchanges, and data persistence.

## 🚀 Project Objectives

Improve the original Pokebox with a cleaner and more modular architecture.

Integrate JavaScript to enhance user interaction and dynamic content.

Add a PHP backend to handle all server-side logic, database interactions, and user actions.

Use PokeAPI to fetch Pokémon data in real time.

Implement an authentication system.

Allow users to collect, like, and exchange cards.

## 🧩 Main Features

### 👥 Friend System

Users can send and accept friend requests.

Friends can view each other's profiles and card collections.

Enables easier access to trading between friends.

Future expansion planned for messaging and friend activity notifications.

### 🔐 Authentication & User Management

User registration and login system.

Personal profile with saved owned cards.

Database support for storing user cards, likes, and exchanges.

### 🃏 Pokémon Card System

Fetch Pokémon data using PokeAPI.

Display individual cards or a full catalog.

Users can "like" cards — likes are saved in the database.

Cards can be assigned to users.

### 🔄 Card Exchange System

Users can propose card trades with other players.

Both users must validate the exchange.

Automatic database update of owned cards after the trade.

### 🌐 Project Pages

Landing Page: Presentation of Pokebox v2.

Card Catalog: Displays all available Pokémon cards via PokeAPI.

User Dashboard: Shows owned cards, likes, and exchange options.

Exchange System: Interface to trade cards between users.


### 🛠️ Technologies Used

HTML / CSS for layout and styling

JavaScript for dynamic functionality and API interaction

PHP for backend logic and data management

MySQL for storing users, cards, likes, and exchanges

PokeAPI for Pokémon card data

Environment variables for securing sensitive configuration:

Database credentials

API URLs

## 🔧 Installation

1. Install a local server environment (Laragon, XAMPP, MAMP, etc.).

2. Clone the repository:
```bash
git clone https://github.com/yourusername/pokeboxv2.git
```

3. Start a local PHP server:

```
http://localhost:8000
```


📌 Notes

Pokebox v2 is a continuously evolving project — new features, improvements, and refinements will be added over time. 
The project is never considered “finished” and will keep growing as new ideas and needs arise.
