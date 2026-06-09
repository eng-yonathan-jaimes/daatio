1. Client Debt Management
2. Gold Purchase Management

---

# Project Overview

## Application Purpose

The application manages:

* Clients who owe money (credit/debt tracking)
* Clients who sell gold to the business
* Clients who buy gold from the business
* Payments, debts, settlements, and transaction history
* Reporting (PDF and printing)

---

# Module 1: Authentication

## Login

### Features

* User login using:

  * Username
  * Password

### Additional Features

* Forgot Password option

### Acceptance Criteria

* User can log in with valid credentials.
* Invalid credentials display an error.
* User can recover password through the recovery process.

Source: Pages 1-2. 

---

# Module 2: Main Navigation

After login, the user should see a navigation menu.

## Menu Items

* Clients
* Gold Purchase
* Settings
* Logout

### Acceptance Criteria

* Menu is accessible from all pages.
* Clicking a menu item redirects to its corresponding module.

Source: Pages 1-2.  

---

# Module 3: Client Debt Management

This module manages customers who owe money.

---

## Dashboard

### Statistics Panel

Display:

* Total Clients
* Total Debt
* Total Amount Paid
* Outstanding Balance

### Search

* Search clients by name

### Debtors List

Display clients who currently have debt.

### Acceptance Criteria

* Statistics update automatically.
* Search returns matching clients.
* Only clients with active debt appear in the debtors list.

Source: Page 2. 

---

## Create Client

### Button

* New Client

### Fields

Required:

* Full Name
* Phone Number

Optional:

* Notes (if desired)

### Actions

* Save
* Cancel

### Confirmation Dialogs

Before saving:

* "Are you sure you want to save this client?"

Before canceling:

* "Are you sure you want to cancel?"

### Acceptance Criteria

* Required fields must be completed.
* Confirmation dialog appears before action execution.

Source: Page 3. 

---

## Client Details

Accessible through the Eye Icon.

### Purpose

View complete client history.

### Information Displayed

* Client profile
* Current debt
* Payment history
* Transactions

Source: Page 3. 

---

# Client Detail Actions

---

## 1. Download History PDF

### Purpose

Generate a PDF containing:

* Client information
* Debt history
* Payments
* Current balance

Source: Page 4. 

---

## 2. Print History

### Purpose

Print the same information shown in the PDF.

Source: Page 4. 

---

## 3. Add Debt

### Purpose

Add additional debt to an existing client.

### Fields

Required:

* Debt Amount

Optional:

* Description / Reason

### Actions

* Save
* Cancel

### Confirmation

Show confirmation dialogs before saving or canceling.

### Acceptance Criteria

* Debt amount is mandatory.
* Debt is added to current balance.
* Transaction is recorded in history.

Source: Page 4. 

---

## 4. Payment (Abonar)

### Purpose

Register a payment against a debt.

### Fields

Required:

* Payment Amount

Optional:

* Note

### Additional Feature

Display payment history.

### Acceptance Criteria

* Payment reduces outstanding balance.
* Payment is stored in transaction history.

Source: Page 5. 

---

## 5. Settle Debt (Liquidar)

### Purpose

Mark debt as fully settled.

### Actions

* Liquidate
* Cancel

### Business Rules

When settled:

* Client remains in database.
* Client is removed from active debtors list.

### Acceptance Criteria

* Historical data remains intact.
* Client no longer appears in debt list.

Source: Page 6. 

---

# Module 4: Gold Purchase Management

This module manages customers who sell gold to the business.

---

## Dashboard

### Statistics

Display:

* Total Clients
* Total Amount Owed To Clients
* Total Paid
* Outstanding Balance

### Search

Search clients.

### Balance List

Display clients with positive balances.

### Acceptance Criteria

* Statistics update automatically.
* Search returns matching records.
* Clients with positive balances appear in the list.

Source: Page 7. 

---

## Create Gold Purchase Client

### Fields

Required

* Full Name
* Phone Number
* Total Amount Paid

Optional

* Gold Weight
* Gold Price Per Gram / Castellano

### Actions

* Save
* Cancel

### Acceptance Criteria

* Required fields validated.
* Optional fields can remain empty.

Source: Page 8. 

---

## Gold Purchase Client Details

Accessible through the Eye Icon.

### Information

Display:

* Client information
* Purchase details
* Balance history
* Payment history

Source: Page 9. 

---

# Gold Purchase Detail Actions

---

## 1. Download History

### Include

* Initial balance
* Current balance
* Money delivered records

Source: Page 9. 

---

## 2. Print History

Print complete history.

Source: Page 9. 

---

## 3. Deliver Money

### Purpose

Register money given to a client.

### Fields

Required

* Amount Delivered
* Recipient Name

Optional

* Note

### Actions

* Accept
* Cancel

### Confirmation

Display confirmation dialog before execution.

### Acceptance Criteria

* Delivery recorded in history.
* Balance updated correctly.

Source: Page 10. 

---

## 4. Liquidate

### Purpose

Close the client's active balance.

### Business Rules

When liquidated:

* Client remains in database.
* Client is removed from positive-balance list.
* Historical records remain available.

### Confirmation

Show confirmation dialog before execution.

Source: Page 11. 

---

# Recommended Database Entities

Based on the requirements, the system should contain at least:

## Users

* id
* username
* password
* email

## Clients

* id
* full_name
* phone
* type (DEBTOR / GOLD_SELLER)
* created_at

## DebtTransactions

* id
* client_id
* amount
* description
* transaction_type (DEBT, PAYMENT)
* created_at

## GoldTransactions

* id
* client_id
* gold_weight
* price_per_gram
* total_amount
* created_at

## MoneyDeliveries

* id
* client_id
* amount
* recipient_name
* note
* created_at

---

# Missing Requirements (Should Be Clarified)

Before development, I would ask the client:

1. Can a client belong to both modules simultaneously?
2. How is password recovery performed?

   * Email?
   * SMS?
3. Are multiple users supported?
4. Should reports include company logo?
5. Should PDF reports have signatures?
6. What currency format is used?
7. Is audit logging required?
8. Should deleted/liquidated records be restorable?
9. Should there be user roles (Admin, Employee)?
10. Is the gold price automatically calculated or entered manually?