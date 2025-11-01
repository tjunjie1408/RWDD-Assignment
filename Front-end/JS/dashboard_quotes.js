document.addEventListener('DOMContentLoaded', () => {
    const quotes = [
        { text: "The only way to do great work is to love what you do.", author: "Steve Jobs" },
        { text: "Success is not final, failure is not fatal: it is the courage to continue that counts.", author: "Winston Churchill" },
        { text: "Believe you can and you're halfway there.", author: "Theodore Roosevelt" },
        { text: "The future belongs to those who believe in the beauty of their dreams.", author: "Eleanor Roosevelt" },
        { text: "Don't watch the clock; do what it does. Keep going.", author: "Sam Levenson" },
        { text: "Efficiency is doing things right; effectiveness is doing the right things.", author: "Peter Drucker" },
        { text: "I have not failed. I've just found 10,000 ways that won't work.", author: "Thomas Edison" },
        { text: "Whether you think you can, or you think you can't - you're right.", author: "Henry Ford" },
        { text: "Perfection is not attainable, but if we chase perfection we can catch excellence.", author: "Vince Lombardi" },
        { text: "It always seems impossible until it's done.", author: "Nelson Mandela" },
        { text: "A person who never made a mistake never tried anything new.", author: "Albert Einstein" },
        { text: "Your most unhappy customers are your greatest source of learning.", author: "Bill Gates" },
        { text: "Well done is better than well said.", author: "Benjamin Franklin" },
        { text: "The journey of a thousand miles begins with a single step.", author: "Lao Tzu" },
        { text: "Be the change that you wish to see in the world.", author: "Mahatma Gandhi" },
        { text: "I've learned that people will forget what you said, people will forget what you did, but people will never forget how you made them feel.", author: "Maya Angelou" },
        { text: "It does not matter how slowly you go as long as you do not stop.", author: "Confucius" },
        { text: "We are what we repeatedly do. Excellence, then, is not an act, but a habit.", author: "Aristotle" },
        { text: "The best way to predict the future is to invent it.", author: "Alan Kay" },
        { text: "Luck is what happens when preparation meets opportunity.", author: "Seneca" },
        { text: "I've failed over and over and over again in my life. And that is why I succeed.", author: "Michael Jordan" },
        { text: "Simplicity is the ultimate sophistication.", author: "Leonardo da Vinci" },
        { text: "What you get by achieving your goals is not as important as what you become by achieving your goals.", author: "Zig Ziglar" },
        { text: "If you're offered a seat on a rocket ship, don't ask what seat! Just get on.", author: "Sheryl Sandberg" },
        { text: "Great minds discuss ideas; average minds discuss events; small minds discuss people.", author: "Eleanor Roosevelt" },
        { text: "You miss 100% of the shots you don't take.", author: "Wayne Gretzky" },
        { text: "An unexamined life is not worth living.", author: "Socrates" },
        { text: "The purpose of our lives is to be happy.", author: "Dalai Lama" },
        { text: "Get busy living or get busy dying.", author: "Stephen King" },
        { text: "The secret of change is to focus all of your energy, not on fighting the old, but on building the new.", author: "Socrates" },
        { text: "Start with the end in mind.", author: "Stephen Covey" },
        { text: "Move fast and break things. Unless you are breaking things, you are not moving fast enough.", author: "Mark Zuckerberg" },
        { text: "If you cannot do great things, do small things in a great way.", author: "Napoleon Hill" },
        { text: "The hard days are what make you stronger.", author: "Aly Raisman" },
        { text: "If you want to lift yourself up, lift up someone else.", author: "Booker T. Washington" }
    ];

    const quoteText = document.getElementById('quote-text');
    const quoteAuthor = document.getElementById('quote-author');
    const refreshButton = document.getElementById('quote-refresh');

    function showRandomQuote() {
        const randomIndex = Math.floor(Math.random() * quotes.length);
        quoteText.textContent = `"${quotes[randomIndex].text}"`;
        quoteAuthor.textContent = `- ${quotes[randomIndex].author}`;
    }

    if (refreshButton) {
        refreshButton.addEventListener('click', () => {
            showRandomQuote();
            refreshButton.classList.add('rotate-animation');
            // Remove the class after the animation completes
            refreshButton.addEventListener('animationend', () => {
                refreshButton.classList.remove('rotate-animation');
            }, { once: true }); // Ensure the event listener is removed after one use
        });
    }

    // Show initial quote
    showRandomQuote();
});