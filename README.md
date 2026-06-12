# Torten Bern — Copilot

Repository for the Torten Bern website (branch copilot/torten-bern-site).
This project is a WooCommerce-based cake ordering site for "Torten Bern" (logo: Berner Bär with Sahne placeholder).

Installation (quick):
1. Clone repo and check out branch `copilot/torten-bern-site`.
2. Copy files into a WordPress install (or use the repo as the WordPress root).
3. Activate theme `torten-bern` (Appearance → Themes) and plugin `torten-bern-core`.
4. Install & activate WooCommerce and (optionally) the official Stripe plugin for WooCommerce.
5. In WP Admin → Torten Bern → Settings configure Twint QR (optional) and demo products.

Testing payments:
- Stripe: install WooCommerce Stripe Payment Gateway and set keys to Test mode.
- Twint: Use the Twint-QR generated on the Thank You page; confirm payment manually in the order admin.

Important: This is a demo/test implementation. Do not use on production without reviewing security and payment settings.
