// Khalti payment integration
function initializeKhaltiPayment(config) {
    let checkout = new KhaltiCheckout(config);
    
    document.getElementById('payment-button').onclick = function () {
        checkout.show({amount: config.amount});
    }
}

function handlePaymentSuccess(response) {
    showAlert('Payment successful!', 'success');
    window.location.href = '/user/dashboard.php';
}

function handlePaymentError(error) {
    showAlert('Payment failed: ' + error.message, 'danger');
    console.error(error);
} 