<?php
// index.php
require_once 'fonctions.php';

$saladesAutorisees = verifierDisponibiliteSalades();
$produits = recupererCatalogue($pdo);
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Pause Fruitée - Commandes</title>
    <script src="https://cdn.jsdelivr.net/npm/@tailwindcss/browser@4"></script>
    <!-- On inclut notre fichier JavaScript externe -->
    <script src="panier.js" defer></script>
</head>
<body class="bg-gradient-to-br from-green-50 to-amber-50 min-h-screen text-gray-800 antialiased font-sans">

    <header class="text-center py-8 px-4 bg-white shadow-sm border-b border-green-100">
        <h1 class="text-4xl font-extrabold text-green-600 tracking-tight mb-2">🍓 Pause Fruitée 🍌</h1>
        <p class="text-lg text-gray-600 max-w-md mx-auto">Savourez la fraîcheur de nos salades de fruits artisanales et de nos yaourts onctueux.</p>
    </header>

    <main class="max-w-6xl mx-auto px-4 py-10">
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-10 items-start">
            
            <!-- MENU -->
            <section class="bg-white p-6 rounded-2xl shadow-sm border border-gray-100">
                <h2 class="text-2xl font-bold text-gray-800 mb-6 border-b pb-3 border-gray-100">📋 Notre Menu</h2>

                <?php if (!$saladesAutorisees): ?>
                    <div class="p-4 mb-6 text-sm text-amber-800 bg-amber-50 rounded-xl border border-amber-200">
                        ℹ️ Les commandes de <strong>Salades de Fruits</strong> sont ouvertes du Lundi au Mercredi. Découvrez nos Yaourts aujourd'hui !
                    </div>
                <?php endif; ?>

                <div class="space-y-4">
                    <?php foreach ($produits as $produit): ?>
                        <?php 
                            $nomMinuscule = strtolower($produit['nom']);
                            $isSalade = (strpos($nomMinuscule, 'salade') !== false);
                            $isIndisponible = ($isSalade && !$saladesAutorisees);
                            
                            // Compositions des produits
                            $composition = match(true) {
                                str_contains($nomMinuscule, 'standard') => "Papaye, Mangue, Banane, Pomme, jus d'orange pressé.",
                                str_contains($nomMinuscule, 'exotique') => "Ananas, Kiwi, Raisins, Fraises, éclats de coco sauvage.",
                                str_contains($nomMinuscule, 'nature') => "Yaourt brassé maison, texture onctueuse, pur sucre de canne.",
                                str_contains($nomMinuscule, 'gourmet') => "Base yaourt avec coulis de fruits frais et morceaux croquants.",
                                default => ""
                            };
                        ?>
                        
                        <div class="p-4 rounded-xl border <?php echo $isIndisponible ? 'bg-gray-50 text-gray-400 border-gray-200 opacity-60' : 'bg-white border-gray-100 shadow-sm'; ?>">
                            <div class="flex justify-between items-start gap-4">
                                <div>
                                    <span class="font-bold text-lg"><?php echo htmlspecialchars($produit['nom']); ?></span>
                                    <p class="text-xs text-gray-500 italic mt-0.5"><?php echo $composition; ?></p>
                                    <?php if ($isIndisponible): ?>
                                        <span class="inline-block mt-1 text-[11px] font-bold text-red-500 bg-red-50 px-2 py-0.5 rounded-full">Indisponible (Retour lundi)</span>
                                    <?php endif; ?>
                                </div>
                                <div class="text-right min-w-[100px]">
                                    <span class="font-extrabold text-green-600"><?php echo $produit['prix']; ?> FCFA</span>
                                    <?php if (!$isIndisponible): ?>
                                        <button type="button" data-nom="<?php echo htmlspecialchars($produit['nom']); ?>" data-prix="<?php echo $produit['prix']; ?>" class="block mt-3 w-full px-3 py-1.5 bg-green-500 hover:bg-green-600 text-white text-xs font-bold rounded-lg shadow-sm">
                                            Ajouter
                                        </button>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </section>

            <!-- LIVRAISON -->
            <section class="bg-white p-6 rounded-2xl shadow-sm border border-gray-100">
                <h2 class="text-2xl font-bold text-gray-800 mb-6 border-b pb-3 border-gray-100">🛵 Finaliser ma Commande</h2>
                
                <form id="formulaire-commande" action="traiter_commande.php" method="POST" class="space-y-4">
                    <div>
                        <label class="block text-xs font-bold uppercase text-gray-600 mb-1">Nom complet *</label>
                        <input type="text" name="nom_client" required class="w-full rounded-xl border border-gray-200 text-sm p-3 bg-gray-50/50">
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-xs font-bold uppercase text-gray-600 mb-1">Téléphone *</label>
                            <input type="tel" name="telephone" placeholder="Ex: 90 00 00 00" required class="w-full rounded-xl border border-gray-200 text-sm p-3 bg-gray-50/50">
                        </div>
                        <div>
                            <label class="block text-xs font-bold uppercase text-gray-600 mb-1">Heure de livraison *</label>
                            <input type="time" name="heure_livraison" required class="w-full rounded-xl border border-gray-200 text-sm p-3 bg-gray-50/50">
                        </div>
                    </div>

                    <div>
                        <label class="block text-xs font-bold uppercase text-gray-600 mb-1">Lieu de livraison *</label>
                        <input type="text" name="lieu_livraison" placeholder="Quartier, repères..." required class="w-full rounded-xl border border-gray-200 text-sm p-3 bg-gray-50/50">
                    </div>

                    <div>
                        <label class="block text-xs font-bold uppercase text-gray-600 mb-1">Lien de localisation GPS</label>
                        <input type="url" name="localisation_gps" placeholder="Lien Google Maps" class="w-full rounded-xl border border-gray-200 text-sm p-3 bg-gray-50/50">
                    </div>

                    <div>
                        <label class="block text-xs font-bold uppercase text-gray-600 mb-1">Notes / Préférences</label>
                        <textarea name="notes_preferences" rows="3" placeholder="Ex: Sans banane..." class="w-full rounded-xl border border-gray-200 text-sm p-3 bg-gray-50/50"></textarea>
                    </div>

                    <div class="bg-gray-50 p-4 rounded-xl border border-gray-100 space-y-2 mt-4">
                        <h3 class="text-sm font-bold text-gray-700 uppercase">Résumé de votre panier</h3>
                        <div id="panier-liste" class="text-sm text-gray-500 italic">Votre panier est vide.</div>
                        <div class="flex justify-between items-center pt-2 border-t font-bold text-gray-800">
                            <span>Total :</span><span id="panier-total" class="text-green-600 text-lg">0 FCFA</span>
                        </div>
                    </div>

                    <input type="hidden" name="details_commande" id="details_commande_input" required>

                    <button type="button" onclick="ouvrirConfirmation()" class="w-full py-4 bg-green-600 hover:bg-green-700 text-white font-extrabold rounded-xl shadow-md uppercase mt-6 text-sm">
                        Vérifier ma commande
                    </button>
                </form>
            </section>
        </div>
    </main>

    <!-- FENÊTRE MODALE DE CONFIRMATION -->
    <div id="modal-confirmation" class="hidden fixed inset-0 bg-gray-900/60 backdrop-blur-sm flex items-center justify-center p-4 z-50">
        <div class="bg-white rounded-2xl shadow-xl max-w-md w-full p-6 space-y-6 transform transition-all scale-95 opacity-0 duration-300" id="modal-content">
            <div class="text-center">
                <h3 class="text-xl font-bold text-gray-900">📝 Vérification de votre commande</h3>
            </div>
            <div class="space-y-4 border-y py-4 border-gray-100 text-sm">
                <div><span class="block text-xs font-bold uppercase text-gray-400">Articles</span><p id="confirm-articles" class="text-gray-800 font-medium mt-1"></p></div>
                <div class="grid grid-cols-2 gap-4">
                    <div><span class="block text-xs font-bold uppercase text-gray-400">Client</span><p id="confirm-nom" class="text-gray-800 font-medium mt-1"></p></div>
                    <div><span class="block text-xs font-bold uppercase text-gray-400">Téléphone</span><p id="confirm-tel" class="text-gray-800 font-medium mt-1"></p></div>
                </div>
                <div class="grid grid-cols-2 gap-4">
                    <div><span class="block text-xs font-bold uppercase text-gray-400">Lieu</span><p id="confirm-lieu" class="text-gray-800 font-medium mt-1"></p></div>
                    <div><span class="block text-xs font-bold uppercase text-gray-400">Heure</span><p id="confirm-heure" class="text-gray-800 font-medium mt-1"></p></div>
                </div>
                <div id="confirm-gps-container"><span class="block text-xs font-bold uppercase text-gray-400">GPS</span><p id="confirm-gps" class="text-gray-800 font-medium mt-1 truncate"></p></div>
                <div id="confirm-notes-container"><span class="block text-xs font-bold uppercase text-gray-400">Notes</span><p id="confirm-notes" class="text-gray-600 italic mt-1"></p></div>
                <div class="flex justify-between items-center pt-2 font-bold text-gray-900"><span>Total :</span><span id="confirm-total" class="text-green-600 text-xl"></span></div>
            </div>
            <div class="flex gap-3">
                <button type="button" onclick="fermerConfirmation()" class="flex-1 py-3 bg-gray-100 text-gray-700 font-bold rounded-xl text-sm">Modifier</button>
                <button type="button" onclick="soumettreFormulaireDéfinitif()" class="flex-1 py-3 bg-green-600 text-white font-bold rounded-xl text-sm shadow-md">Tout est bon !</button>
            </div>
        </div>
    </div>
</body>
</html>