# CodeIgniter 4 Web Platform

A versatile, production‑ready CodeIgniter 4 application providing user authentication, AI integration, cryptocurrency analytics, and payment processing. Ideal for launching a SaaS product, personal brand, or developer portfolio.

## 🎯 What’s Inside

- **Authentication**: Registration, login, email verification, password reset, role‑based access
- **AI Service**: Google Gemini chat with conversation memory
- **Crypto Tools**: Real-time Bitcoin & Litecoin address balance & transactions
- **Payments**: Paystack integration supporting M‑Pesa, Airtel, and card payments
- **Admin Panel**: User management, financial overview, email campaign tools
- **Blog & Affiliates**: Content publishing and referral tracking

## 🏗️ Technical Highlights

- Framework: CodeIgniter 4 (latest)
- PHP: 7.4+ (8.2 recommended)
- Database: MySQL/MariaDB (via standard CI4 model)
- Frontend: Bootstrap 5, responsive design
- Security: CSRF, XSS filtering, securepassword hashing, environment config
- Deployment: Includes `setup.sh` for Ubuntu servers; also works with any LAMP stack

## ⚡ Quick Start

### One‑line setup (Ubuntu)

```bash
git clone https://github.com/nehemiaobati/codeigniter.git
cd codeigniter
sudo ./setup.sh
```

Then configure your `.env` file with API keys and database credentials.

### Development mode

```bash
composer install
cp env .env
./spark serve
```

Open `http://localhost:8000` in your browser.

## 📚 Documentation

See `documentation.md` for:

- Complete installation guide
- Architectural overview
- Feature walkthroughs
- Configuration reference
- Maintenance & troubleshooting

## 🔒 Security Notes

- Keep your `.env` file out of version control.
- Use HTTPS in production.
- Regularly update dependencies (`composer update`).
- Change default admin credentials after installation.

## 📄 License

MIT

---

*A solid foundation for your next web project.*
