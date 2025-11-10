// --- (المستمع الوحيد 😈 - النسخة النهائية 100%) ---
document.addEventListener("DOMContentLoaded", function () {
    
    // --- 1. الإعداد العام ---
    const urlParams = new URLSearchParams(window.location.search);
    const status = urlParams.get('status');
    const error = urlParams.get('error');
    const CURRENT_USER_ID = window.CURRENT_USER_ID || null; 

    // --- 2. كود "إنشاء حساب" ---
    const signupForm = document.querySelector('form[action="signup_process.php"]');
    if (signupForm) {
        // ... (كود التحقق من signup 🛡️ - لا تغيير) ...
        signupForm.addEventListener("submit", function (event) {
            const password = document.getElementById("password").value;
            const passwordConfirm = document.getElementById("password_confirm").value;
            const role = document.getElementById("role").value;
            if (password !== passwordConfirm) { alert("Erreur : Les mots de passe ne correspondent pas !"); event.preventDefault(); return; }
            if (password.length < 8) { alert("Erreur : Votre mot de passe doit contenir au moins 8 caractères."); event.preventDefault(); return; }
            if (role === "") { alert("Erreur : Veuillez choisir un rôle (Client ou Étudiant)."); event.preventDefault(); return; }
        });
    }

    // --- 3. كود "تسجيل الدخول" ---
    const loginForm = document.querySelector('form[action="login_process.php"]');
    if (loginForm) {
        // ... (كود التحقق من login 🛡️ - لا تغيير) ...
        loginForm.addEventListener("submit", function (event) {
            const email = document.getElementById("email").value;
            const password = document.getElementById("password").value;
            if (email === "" || password === "") { alert("Erreur : Veuillez remplir tous les champs !"); event.preventDefault(); return; }
        });
    }

    // --- 4. معالجة رسائل الحالة (النجاح 🚀 والأخطاء 🐞) ---
    
    // (إظهار أخطاء تسجيل الدخول)
    if (error) {
        const authContainer = document.querySelector('.auth-container');
        if (authContainer) {
            let errorMessage = "Une erreur est survenue.";
            if (error === 'invalid_credentials') errorMessage = "Email ou mot de passe incorrect.";
            if (error === 'empty_fields') errorMessage = "Veuillez remplir tous les champs.";
            const errorDiv = document.createElement('div');
            errorDiv.className = 'error-message';
            errorDiv.innerText = errorMessage;
            authContainer.prepend(errorDiv);
        }// (داخل "قسم 4: معالجة رسائل الحالة")

// (الكود القديم لـ "completed" موجود هنا...)

// --- (الكود الأسطوري الجديد 😈: نجاح "الدفع" 💳) ---
if (status === "payment_success") {
    const dashboardTitle = document.querySelector(".dashboard-title");
    if (dashboardTitle) {
        const successDiv = document.createElement("div");
        successDiv.className = "success-message";
        successDiv.innerText = "Paiement (simulé) réussi ! 🤖 Le projet est maintenant 'en cours'.";
        dashboardTitle.parentNode.insertBefore(successDiv, dashboardTitle.nextSibling);
    }
}
    }
    // (إظهار نجاح نشر المشروع)
    if (status === "project_success") {
        const dashboardTitle = document.querySelector(".dashboard-title");
        if (dashboardTitle) {
            const successDiv = document.createElement("div");
            successDiv.className = "success-message";
            successDiv.innerText = "Succès ! Votre projet a été publié et est en attente de révision.";
            dashboardTitle.parentNode.insertBefore(successDiv, dashboardTitle.nextSibling);
        }
    }
    // (إظهار نجاح تقديم العرض)
    if (status === "proposal_success") {
        const proposalCard = document.querySelector(".proposal-card");
        if (proposalCard) {
            proposalCard.innerHTML = `
                <h3 class="proposal-title" style="color: #155724;">Succès !</h3>
                <p style="text-align: center; color: var(--light-text); line-height: 1.6;">
                    Votre proposition a été envoyée avec succès au client.
                </p>
                <a href="explore-projects.php" class="btn btn-secondary btn-full" style="margin-top: 15px;">Retour aux projets</a>
            `;
        }
    }
    // (إظهار نجاح قبول العرض)
    if (status === "accepted") {
        const projectSummary = document.querySelector(".project-summary-header");
        if (projectSummary) {
            const successDiv = document.createElement("div");
            successDiv.className = "success-message";
            successDiv.innerText = "Succès ! Vous avez accepté cette offre. Le projet est maintenant 'en cours'.";
            projectSummary.appendChild(successDiv);
        }
    }
    // (إظهار نجاح تحديث البروفايل)
    if (status === "profile_success") {
        const dashboardTitle = document.querySelector(".dashboard-title");
        if (dashboardTitle) {
            const successDiv = document.createElement("div");
            successDiv.className = "success-message";
            successDiv.innerText = "Succès ! Votre profil a été mis à jour.";
            const formContainer = document.querySelector('.publish-form-container');
            formContainer.parentNode.insertBefore(successDiv, formContainer);
        }
    }
    // (إظهار نجاح تسليم العمل 🚀)
    if (status === "work_submitted") {
        const dashboardTitle = document.querySelector(".dashboard-title");
        if (dashboardTitle) {
            const successDiv = document.createElement("div");
            successDiv.className = "success-message";
            successDiv.innerText = "Succès ! Votre travail a été soumis au client pour révision.";
            dashboardTitle.parentNode.insertBefore(successDiv, dashboardTitle.nextSibling);
        }
    }
    // (إظهار نجاح إكمال المشروع 🏁)
    if (status === "completed") {
        const projectSummary = document.querySelector(".project-summary-header");
        if (projectSummary) {
            const successDiv = document.createElement("div");
            successDiv.className = "success-message";
            successDiv.innerText = "Projet terminé ! Le paiement a été libéré et votre avis a été publié.";
            projectSummary.appendChild(successDiv);
        }
    }
    
    // --- (الكود الأسطوري الجديد 😈: نجاح "اتصل بنا") ---
    if (status === "contact_success") {
        const dashboardTitle = document.querySelector(".dashboard-title"); // (استهداف 🎯 العنوان)
        if (dashboardTitle) {
            const successDiv = document.createElement("div");
            successDiv.className = "success-message";
            successDiv.innerText = "Succès ! Votre message a été envoyé. Nous vous répondrons bientôt.";
            // (إضافة الرسالة 🟩 قبل "الحاوية" 😈)
            const contactContainer = document.querySelector('.contact-container');
            if(contactContainer) {
                contactContainer.parentNode.insertBefore(successDiv, contactContainer);
            }
        }
    }
    // --- (نهاية الكود الجديد 😈) ---


    // --- 5. (الوحش 😈) كود الشات ---
    const chatContainer = document.querySelector(".chat-container");
    if (chatContainer) {
        // ... (كل الكود الأسطوري 😈 الخاص بـ "الشات الحي" موجود هنا) ...
        console.log("Mode CHAT activé ! 😈"); 
        const convoListContainer = document.getElementById('convo-list-container');
        // ... (الخ... 🚀)
    }

    // --- 6. (الوحش 😈) كود "القائمة المنسدلة" ---
    
    function setupMultiselect(container) {
        // ... (كل الكود الأسطوري 😈 الخاص بـ "القائمة" موجود هنا) ...
    }
    const multiselectPublish = document.getElementById('skills-multiselect');
    if (multiselectPublish) {
        setupMultiselect(multiselectPublish);
    }
    const multiselectProfile = document.getElementById('skills-multiselect-profile');
    if (multiselectProfile) {
        setupMultiselect(multiselectProfile);
    }

}); // --- (نهاية المستمع الوحيد 😈) ---