// Khalti payment integration
function initializeKhaltiPayment(config) {
    // Validate amount is within Khalti limits
    if (config.amount < 1000 || config.amount > 100000) { // 10 Rs to 1000 Rs in paisa
        alert('Payment amount must be between Rs. 10 and Rs. 1000');
        return;
    }

    let checkout = new KhaltiCheckout({
        publicKey: config.publicKey,
        productIdentity: config.productIdentity,
        productName: config.productName,
        productUrl: config.productUrl,
        amount: config.amount,  // Already in paisa from payment.php
        eventHandler: {
            onSuccess(payload) {
                // hits success url after successful payment
                $.ajax({
                    url: SITE_URL + '/process_payment.php',
                    type: 'POST',
                    data: {
                        token: payload.token,
                        amount: config.amount,
                        subscription_id: config.productIdentity
                    },
                    success: function(response) {
                        if (response.success) {
                            window.location.href = SITE_URL + '/user/dashboard.php?payment=success';
                        } else {
                            alert(response.error || 'Payment verification failed. Please contact support.');
                        }
                    },
                    error: function(xhr) {
                        alert('Payment verification failed. Please try again.');
                        console.error(xhr.responseText);
                    }
                });
            },
            onError(error) {
                console.log(error);
                alert('Payment failed. Please try again.');
            },
            onClose() {
                console.log('Widget is closing');
            }
        },
        paymentPreference: ["KHALTI"],
    });

    document.getElementById('payment-button').onclick = function () {
        checkout.show({popupHeight: 600});
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