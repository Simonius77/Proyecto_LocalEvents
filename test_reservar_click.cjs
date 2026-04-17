const puppeteer = require('puppeteer');

(async () => {
    const browser = await puppeteer.launch({ headless: 'new', args: ['--no-sandbox'] });
    const page = await browser.newPage();
    
    page.on('console', msg => console.log('PAGE LOG:', msg.text()));
    page.on('pageerror', error => console.log('PAGE ERROR:', error.message));

    try {
        console.log("Going to home page first...");
        await page.goto('http://127.0.0.1:8000/', { waitUntil: 'networkidle0' });
        
        console.log("Looking for an event link...");
        // Click on the first event link containing 'eventos/'
        const eventLink = await page.evaluate(() => {
            const links = Array.from(document.querySelectorAll('a'));
            const eLink = links.find(l => l.href.includes('/eventos/'));
            return eLink ? eLink.href : null;
        });
        
        if (!eventLink) {
            console.log("No event found. Going to fallback /eventos/1");
            await page.goto('http://127.0.0.1:8000/eventos/1', { waitUntil: 'networkidle0' });
        } else {
            console.log(`Found event link ${eventLink}. Navigating...`);
            await page.goto(eventLink, { waitUntil: 'networkidle0' });
        }
        
        console.log("On event page. Clicking Reservar...");
        
        await page.evaluate(() => {
            const btns = Array.from(document.querySelectorAll('button'));
            const resBtn = btns.find(b => b.textContent && b.textContent.includes('Reservar'));
            if(resBtn) {
                console.log("Button found! Clicking...");
                resBtn.click();
            } else {
                console.log("Button NOT found.");
            }
        });
        
        // Wait for navigation and Vue rendering
        await page.waitForTimeout(3000);
        
        const content = await page.content();
        console.log("Current URL after click:", page.url());
        
        if (content.includes('loginForm') || document.querySelector('.login-card')) {
            console.log("Found login card class in HTML.");
        } else {
            console.log("Login card NOT found.");
        }
    } catch (e) {
        console.log("Failed:", e.message);
    }
    
    await browser.close();
})();
