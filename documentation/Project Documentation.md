# Daatio — Project Documentation

## Overview

Daatio is a metal-trading credit/debit management application for small businesses. Operators (tenants) manage customers who trade metals (gold, silver, etc.) — the app tracks who owes whom, records every financial operation, manages stock, and generates reports.

The app name "Daatio" is derived from the business domain; the Spanish-language design documents target a Latin American market.

---

## Business Domain

The system handles two kinds of customer financial relationships, always tracked from the **client's perspective**:

| Direction | Transaction Type | What it means | Balance effect |
|---|---|---|---|
| Client buys from business (on credit) | **Buying** | Client owes the business | **Debit** (negative) |
| Client sells to business (metal purchase) | **Selling** | Business owes the client | **Favor** (positive) |

Every transaction creates:
1. A **client_order** (the deal record)
2. A **transaction** (the audit trail entry)
3. A **client_state** update (the running balance)

### Balance convention (signed amounts)

- `> 0` = Favor (client has money in their favor)
- `< 0` = Debit (client owes the business)
- `= 0` = Settled (fully paid, no balance)

State is computed automatically from the amount sign.

---

## Technology Stack

- **Backend:** PHP 8.2, Laravel 12
- **Database:** MySQL (MariaDB via XAMPP)
- **Frontend:** Blade templates + vanilla CSS + vanilla JavaScript
- **Auth:** Laravel session-based (web), Sanctum token-based (API)
- **i18n:** Laravel localization (English / Spanish)
- **Dependencies:** Composer only — no npm, no Vite, no JS framework

---

## Database Schema

### User & Auth

| Table | Purpose |
|---|---|
| `user` | Operator/tenant accounts |
| `user_login_history` | Every login attempt recorded (IP, device, timestamp) |
| `user_recovery_history` | Password reset attempts (method, success, IP, device) |
| `password_reset_tokens` | Laravel password reset tokens |

### Subscription

| Table | Purpose |
|---|---|
| `subscription` | Plan definitions (Trial 14d, Monthly 30d) |
| `user_subscription` | User's active/past subscriptions (start, end, status, trial) |
| `subscription_payment` | Payment records (amount, method, status, reference) |

### Business

| Table | Purpose |
|---|---|
| `store` | Business locations (Physical/Online/Both) linked to user |
| `store_type` | Store categories (e.g., General, Jewelry Shop) |
| `product` | Metal/item types (e.g., Gold 18K, Silver 925) with stock weight, value, currency |
| `client` | Customer records (name, phone, email, document) |
| `client_state` | Per-client-per-store running balance (signed amount, computed state) |
| `client_order` | Deal/agreement record (value, state: Favor/Debit/Settled, due date) |
| `client_list_order` | Line items per order (product, weight, value) |
| `transaction` | Full audit trail — every financial operation (type, amount, state, user, description, linked to order) |

### Relationships

```
user ──< store ──< client_state >── client
user ──< store ──< transaction >── client
user ──< store ──< product
user ──< store ──< client_order >── client
user ──< store ──< client_list_order >── product
client_order ──< client_list_order
client_order ──< transaction
user ──< transaction (transaction_user_id — audit)
```

---

## Module Architecture (nwidart/laravel-modules)

| Module | Status | Contents |
|---|---|---|
| **Users** | Active | User model, login/recovery models, Sanctum API auth routes |
| **Stores** | Active | Store model, StoreType model, full CRUD controller + routes |
| **Transactions** | Active | Transaction model, full controller (5 operation types + quick store) + routes |
| **Clients** | Active | Client, ClientOrder, ClientListOrder, ClientState models |
| **Products** | Active | Product model, full CRUD controller + routes |
| **Subscriptions** | Loose | Subscription, UserSubscription, SubscriptionPayment models (no module registration) |

---

## Features Implemented

### Authentication
- [x] Registration (first name, last name, country code + phone, email, password)
- [x] Login with "remember me"
- [x] Login history recording (IP, device, timestamp)
- [x] Email verification (6-digit code, shown on-screen in dev)
- [x] Phone verification (6-digit code, shown on-screen in dev)
- [x] Verification chain: email → phone → dashboard
- [x] Password reset (forgot/reset flow, email logged to file in dev)
- [x] Recovery history recording (intent, success, method, IP, device)
- [x] Logout with session invalidation

### Dashboard
- [x] Welcome message with user name
- [x] "+ New Transaction" quick action button
- [x] Stats: Total Customers, Total Debt, Total Paid, Outstanding Balance
- [x] Debtor list with name, phone, amount owed, last transaction date
- [x] Search by name or phone
- [x] Subscription status banner (days remaining, renew/manage link)

### Customer Management
- [x] Customer list with search (name, phone)
- [x] Create customer: first name, last name, country code + phone, email, document type/number
- [x] Customer detail: balance card, contact info, document info
- [x] Action buttons: Add Debt, Register Payment, Metal Purchase, Deliver Money, Settle Account
- [x] Orders section: shows all orders with line items (product + weight)
- [x] Transaction history table with description, type, amount, state, auditor
- [x] Print customer history (opens printable view)

### Transactions
- [x] Full transaction list with type filters: All, Purchases, Debts, Payments, Deliveries, Settlements
- [x] Quick Transaction page with:
  - Client search (keyboard-navigable dropdown, searches by name/phone/document)
  - Direction selector (buying from client / selling to client)
  - Product search + dynamic line items table (keyboard navigation, stock display, add/remove items)
  - Stock validation when selling (blocks if insufficient stock)
  - Total value with real-time thousand-separator formatting
  - Amount paid field with "Pay Total" shortcut
  - Live summary: auto-calculates who owes whom based on direction + remaining balance
  - Partial payment support: remaining = total - paid
- [x] Legacy transaction forms per type (accessed from customer detail)
- [x] Auto-generated transaction descriptions (e.g., "Sold to Client – Gold 18K, Silver 925 – Partial Payment")
- [x] Every transaction linked to a client_order
- [x] Stock auto-update: increases on buy, decreases on sell
- [x] Client_state auto-update with signed amount convention
- [x] Audit trail: every operation stamped with transaction_user_id

### Product Management
- [x] Metal/item types CRUD (name, initial weight, initial value, currency)
- [x] Stock tracking (weight in/out per transaction)
- [x] In Stock / Out of Stock status
- [x] Currency support: USD, COP, EUR, VES, MXN, PEN, ARS, CLP, BRL

### Store Management
- [x] Store CRUD (name, address, type, location)
- [x] Activate/Deactivate toggle
- [x] Store types management (inline add on stores page)
- [x] Auto-create default store on registration

### Subscription
- [x] Two seeded plans: Trial (14d free), Monthly ($29.99/30d)
- [x] Auto-assign trial on registration
- [x] Subscription page: plan details, payment history, available plans
- [x] Renew flow: cancel old → create new → record payment
- [x] Dashboard banner with days remaining

### Reports & Printing
- [x] Reports page with stat cards
- [x] 6 report types: Customer List, Debt History, Payment History, Purchase History, Money Deliveries, Full Audit Log
- [x] Print-optimized views (no sidebar, clean layout, auto-trigger print dialog)
- [x] Print individual customer history
- [x] Print buttons on customer list, customer detail, transactions page

### Internationalization (i18n)
- [x] English + Spanish with 300+ translation keys
- [x] Language switcher (EN/ES buttons in navbar)
- [x] Session-persisted locale via middleware
- [x] All main views translated (sidebar, dashboard, customers, transactions, products, stores, reports, subscription)

### UI/UX
- [x] Responsive sidebar layout
- [x] Footer with copyright
- [x] Favicon support (`public/favicon.ico`)
- [x] Country code dropdown with flags (18 countries) on phone inputs
- [x] Vanilla JS search dropdowns with keyboard navigation (↑↓ Enter Escape)
- [x] Real-time currency formatting on value inputs
- [x] Stock validation alerts
- [x] Confirmation dialogs on destructive actions

---

## URL Routes

### Auth (public)
| Method | URL | Action |
|---|---|---|
| GET | `/login` | Show login form |
| POST | `/login` | Process login |
| POST | `/logout` | Logout |
| GET | `/register` | Show registration form |
| POST | `/register` | Process registration |
| GET/POST | `/password/reset` | Forgot password |
| GET/POST | `/password/reset/{token}` | Reset password |
| GET/POST | `/email/verify` | Email verification |
| GET/POST | `/phone/verify` | Phone verification |
| POST | `/language` | Switch language |

### Authenticated
| Method | URL | Action |
|---|---|---|
| GET | `/dashboard` | Dashboard |
| GET | `/customers` | Customer list |
| GET/POST | `/customers/create` | Create customer |
| GET | `/customers/{id}` | Customer detail |
| GET | `/transactions` | Transaction list |
| GET | `/transactions/new` | Quick transaction |
| POST | `/transactions/quick` | Store quick transaction |
| GET | `/customers/{id}/transactions/create` | Legacy transaction form |
| POST | `/customers/{id}/transactions` | Store legacy transaction |
| GET/POST | `/products` | Product CRUD |
| GET/POST | `/stores` | Store CRUD |
| GET/POST | `/stores/types` | Store type management |
| GET/POST | `/subscription` | Subscription management |
| GET | `/reports` | Reports |
| GET | `/print/customers` | Print customer list |
| GET | `/print/transactions` | Print transactions |
| GET | `/print/customers/{id}` | Print customer history |

---

## Setup & Deployment

### First run
```bash
# Database
php setup.php       # Creates database + all tables + seeds plans

# Or manually
mysql < database.sql
```

### Environment (.env)
```
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=daatio
DB_USERNAME=root
DB_PASSWORD=yourpassword
MAIL_MAILER=log     # Dev: emails logged to storage/logs/laravel.log
```

### Production mail
Change `MAIL_MAILER=smtp` and configure SMTP credentials in `.env`.

---

## Remaining / Out of Scope

- Welcome/landing page content (currently generic placeholder)
- Product create/edit form translations
- Print view translations
- Admin panel for managing all users/stores (single-tenant per user currently)
- Online payment gateway integration (out of scope per spec)
- Banking system integration (out of scope per spec)
- Inventory management beyond weight tracking (out of scope per spec)
- Accounting/tax features (out of scope per spec)
