const puppeteer = require('puppeteer');

(async () => {
    const browser = await puppeteer.launch({ headless: 'new', args: ['--no-sandbox'] });
    const page = await browser.newPage();
    
    page.on('console', msg => console.log('PAGE LOG:', msg.text()));
    page.on('pageerror', error => console.log('PAGE ERROR:', error.message));
    page.on('requestfailed', request => console.log('REQUEST FAILED:', request.url(), request.failure().errorText));

    try {
        await page.goto('http://127.0.0.1:8000/login', { waitUntil: 'networkidle0', timeout: 10000 });
        const content = await page.content();
        console.log("HTML length:", content.length);
        if (content.includes('loginForm') || content.includes('login-card')) {
            console.log("Found login card class in HTML.");
        } else {
            console.log("Login card NOT found.");
        }
        
    } catch (e) {
        console.log("Failed to load:", e.message);
    }
    
    await browser.close();
})();
