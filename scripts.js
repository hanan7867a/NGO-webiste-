document.addEventListener("DOMContentLoaded", function () {
  // Typing Effect
  const donateButton = document.getElementById('donatebtn');
  const joinButton = document.getElementById('joinusbtn');

  const donateText = "What if your kindness today could change a life forever?";
  const joinText = "Join us in spreading hope through education, food, and care.";

  const donateTextElement = document.getElementById('hero-text');
  const joinTextElement = document.getElementById('jointxt');

  let isTyping = false;

  function startTypingEffect(textElement, text) {
    textElement.textContent = ""; // Clear the text
    let index = 0;
    const typingInterval = setInterval(() => {
      if (index < text.length) {
        textElement.textContent += text.charAt(index);
        index++;
      } else {
        clearInterval(typingInterval);
        isTyping = false; // Allow another typing effect
      }
    }, 80);
  }

  donateButton.addEventListener('mouseenter', () => {
    if (isTyping) return;
    isTyping = true;
    startTypingEffect(donateTextElement, donateText);
  });

  joinButton.addEventListener('mouseenter', () => {
    if (isTyping) return;
    isTyping = true;
    startTypingEffect(joinTextElement, joinText);
  });

  // Mobile Navigation Toggle
  const menuToggle = document.querySelector('.menu-toggle');
  const mobileNav = document.querySelector('.mobile-nav');

  menuToggle.addEventListener('click', () => {
    mobileNav.classList.toggle('active');
  });

  // Volunteer Form Modal Toggle
  const openBtn = document.getElementById('joinusbtn');
  const closeBtn = document.getElementById('closeFormBtn');
  const overlay = document.getElementById('volunteerOverlay');

  openBtn.addEventListener('click', () => {
    overlay.style.display = 'flex';
  });

  closeBtn.addEventListener('click', () => {
    overlay.style.display = 'none';
  });
  document.getElementById("donatebtn").addEventListener("click", function () {
    document.getElementById("donationOverlay").style.display = "flex";
  });

  document.getElementById("closeDonateForm").addEventListener("click", function () {
    document.getElementById("donationOverlay").style.display = "none";
  });
  
  
});
