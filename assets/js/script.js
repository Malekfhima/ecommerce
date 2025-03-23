// Fonction pour ajouter un produit au panier
function addToCart(productId) {
    fetch('add_to_cart.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
        },
        body: JSON.stringify({ productId: productId }),
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            alert('Produit ajouté au panier !');
            // Mettre à jour l'affichage du panier (ex: icône du panier)
            updateCartIcon();
        } else {
            alert(data.message);
        }
    });
}

// Fonction pour mettre à jour la quantité d'un produit dans le panier
function updateQuantity(cartId, action) {
    fetch('update_cart.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
        },
        body: JSON.stringify({ cartId: cartId, action: action }),
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            location.reload(); // Recharger la page pour afficher la nouvelle quantité
        } else {
            alert(data.message);
        }
    });
}

// Fonction pour supprimer un produit du panier
function removeFromCart(cartId) {
    if (confirm('Êtes-vous sûr de vouloir supprimer ce produit du panier ?')) {
        fetch('remove_from_cart.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
            },
            body: JSON.stringify({ cartId: cartId }),
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                location.reload(); // Recharger la page pour mettre à jour le panier
            } else {
                alert(data.message);
            }
        });
    }
}

// Fonction pour valider le formulaire de connexion
function validateLoginForm() {
    const email = document.getElementById('email').value;
    const password = document.getElementById('password').value;

    if (!email || !password) {
        alert('Veuillez remplir tous les champs.');
        return false;
    }

    return true;
}

// Fonction pour valider le formulaire d'inscription
function validateRegisterForm() {
    const username = document.getElementById('username').value;
    const email = document.getElementById('email').value;
    const password = document.getElementById('password').value;

    if (!username || !email || !password) {
        alert('Veuillez remplir tous les champs.');
        return false;
    }

    if (password.length < 6) {
        alert('Le mot de passe doit contenir au moins 6 caractères.');
        return false;
    }

    return true;
}

// Fonction pour valider le formulaire de paiement
function validateCheckoutForm() {
    const name = document.getElementById('name').value;
    const email = document.getElementById('email').value;
    const address = document.getElementById('address').value;
    const paymentMethod = document.getElementById('payment_method').value;

    if (!name || !email || !address || !paymentMethod) {
        alert('Veuillez remplir tous les champs.');
        return false;
    }

    return true;
}

// Fonction pour mettre à jour l'icône du panier
function updateCartIcon() {
    fetch('get_cart_count.php')
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            const cartIcon = document.getElementById('cart-icon');
            if (cartIcon) {
                cartIcon.textContent = data.count;
            }
        }
    });
}

// Fonction pour afficher un message d'erreur
function showError(message) {
    const errorDiv = document.createElement('div');
    errorDiv.className = 'error-message';
    errorDiv.textContent = message;
    document.body.appendChild(errorDiv);

    // Supprimer le message après 3 secondes
    setTimeout(() => {
        errorDiv.remove();
    }, 3000);
}

// Fonction pour afficher un message de succès
function showSuccess(message) {
    const successDiv = document.createElement('div');
    successDiv.className = 'success-message';
    successDiv.textContent = message;
    document.body.appendChild(successDiv);

    // Supprimer le message après 3 secondes
    setTimeout(() => {
        successDiv.remove();
    }, 3000);
}

// Écouteurs d'événements pour les formulaires
document.addEventListener('DOMContentLoaded', () => {
    // Validation du formulaire de connexion
    const loginForm = document.getElementById('login-form');
    if (loginForm) {
        loginForm.addEventListener('submit', (e) => {
            if (!validateLoginForm()) {
                e.preventDefault();
            }
        });
    }

    // Validation du formulaire d'inscription
    const registerForm = document.getElementById('register-form');
    if (registerForm) {
        registerForm.addEventListener('submit', (e) => {
            if (!validateRegisterForm()) {
                e.preventDefault();
            }
        });
    }

    // Validation du formulaire de paiement
    const checkoutForm = document.getElementById('checkout-form');
    if (checkoutForm) {
        checkoutForm.addEventListener('submit', (e) => {
            if (!validateCheckoutForm()) {
                e.preventDefault();
            }
        });
    }

    // Mettre à jour l'icône du panier au chargement de la page
    updateCartIcon();
});