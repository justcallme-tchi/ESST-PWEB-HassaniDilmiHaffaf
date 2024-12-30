<?php 
session_start(); 
if (!isset($_SESSION['email'])) { 
    header("Location: Login.htm"); 
    exit(); 
} else echo "session creer";
    ?>
<!DOCTYPE html>
<html lang="fr">
<head> 
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Rechercher des Livres - Bibliothèque ESST</title>
    <link rel="stylesheet" href="../css/StyleUser.css">
    <link rel="stylesheet" href="../css/search_books.css"> 
</head>
<body>

    <!-- Sidebar -->
    <aside class="sidebar">
        <div class="logo">
            <img src="../img/ESST-logo-white-300x233.png" alt="ESST Logo">
        </div>
        <nav>
            <ul>
            <li><a href="home.php">Page d'accueil</a></li>
                <li><a href="user.php" >Dashboard</a></li>
                <li><a href="SearchBooks.php" class="active">Rechercher des Livres</a></li>
                <li><a href="Profile.php">Mon Profil</a></li>
                <li><a href="../php/logout.php">Deconnexion</a></li>
            </ul>
        </nav> 
    </aside>

    <!-- Main Search Content -->
    <main class="dashboard-content">
        <header class="dashboard-header">
            <h1 style="text-align: center;">Rechercher des Livres</h1>
            <p style="text-align: center;">Trouvez vos livres préférés en utilisant le formulaire de recherche ci-dessous.</p>
        </header>

        <!-- Search Form Section -->
        <section id="search-form">
            <form action="" method="get" class="search-books-form">
                <input type="text" id="searchTitle" name="title" placeholder="Rechercher par titre" >
                <input type="text" id="searchAuthor" name="author" placeholder="Rechercher par auteur">
                <input type="text" id="searchYear" name="searchYear" placeholder="Année d'édition">
                <button type="submit" class="btn-search">Rechercher</button>
            </form>
        </section>

        <!-- Search Results Section -->
        <section id="search-results">
            <h2 style="text-align: center;">Résultats de la Recherche</h2>
            <table class="search-results-table">
                <thead>
                    <tr>
                        <th>Titre</th>
                        <th>Auteur</th>
                        <th>Année</th>
                        <th>Categorie</th>
                        <th>Disponibilité</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                   
                </tbody>
            </table>
        </section>
    </main>
    <script src="../js/SearchBooks.js"></script>
</body>
</html>
