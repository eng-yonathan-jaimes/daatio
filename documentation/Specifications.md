# Gold Trading Management System

## Functional Requirements Specification (FRS)

### Version

1.0

### Technology Stack

* Backend: PHP
* Database: MySQL
* Frontend: Existing implementation
* Authentication: Already implemented

---

# 1. Project Overview

The system manages customer transactions related to gold purchasing and credit operations.

The application must allow:

* Customer registration and management.
* Credit (debt) management.
* Payment registration.
* Debt settlement.
* Gold purchase registration.
* Money delivery tracking.
* Historical transaction tracking.
* PDF and printing capabilities.

The system must preserve historical records and never permanently delete customer information.

---

# 2. Business Objectives

The application must help the business:

* Track customers that owe money.
* Track customers with favorable balances.
* Maintain complete transaction history.
* Monitor outstanding balances.
* Generate printable reports.
* Maintain financial transparency.

---

# 3. Functional Modules

## 3.1 Customer Management Module

### Description

Allows creation, searching, and viewing of customer records.

### Features

#### Create Customer

The system shall allow creating a customer with:

* Full Name (Required)
* Phone Number (Required)

Additional optional fields:

* Gold Weight
* Price Per Gram
* Price Per Castellano

#### Validations

* Name cannot be empty.
* Phone number cannot be empty.
* Confirmation dialog before saving.
* Confirmation dialog before cancellation.

---

### Search Customer

The system shall provide:

* Search by name.
* Search by phone number.

---

### Customer Detail View

The system shall display:

* Customer information.
* Current balance.
* Historical transactions.
* Payments made.
* Debts registered.
* Settlements performed.

---

# 4. Credit (Debt) Management Module

## Description

Used when customers receive products or services on credit.

---

## Dashboard Requirements

Display:

* Total Customers
* Total Debt
* Total Paid
* Outstanding Balance

Additionally:

* Search area.
* List of customers with outstanding debt.

---

## Add Debt

### Description

Allows increasing a customer's debt.

### Fields

| Field               | Required |
| ------------------- | -------- |
| Debt Amount         | Yes      |
| Description/Concept | No       |

### Business Rules

* Amount must be greater than zero.
* Customer must exist.
* Transaction must be recorded in history.
* Confirmation required before saving.

---

## Register Payment

### Description

Allows recording a payment toward an outstanding debt.

### Fields

| Field          | Required |
| -------------- | -------- |
| Payment Amount | Yes      |
| Note           | No       |

### Business Rules

* Amount must be greater than zero.
* Payment cannot exceed current debt.
* Transaction must be recorded in history.
* Confirmation required before saving.

---

## Debt Settlement

### Description

Marks a customer's debt as fully paid.

### Business Rules

* Customer remains in database.
* Customer disappears from "Outstanding Debt List".
* Historical records remain available.
* Confirmation required.

---

# 5. Gold Purchase Module

## Description

Used when the business purchases gold from customers.

---

## Dashboard Requirements

Display:

* Total Customers
* Total Amount Paid
* Current Outstanding Balance
* Customers with positive balances

---

## Create Gold Purchase Record

### Fields

| Field             | Required |
| ----------------- | -------- |
| Full Name         | Yes      |
| Phone Number      | Yes      |
| Gold Weight       | No       |
| Price Per Gram    | No       |
| Total Amount Paid | Yes      |

### Business Rules

* Customer must be registered.
* Transaction stored in history.
* Confirmation required before saving.

---

## Customer Gold History

The system shall show:

* Customer information.
* Gold purchase history.
* Balance history.
* Money deliveries.
* Settlements.

---

# 6. Money Delivery Module

## Description

Used when money is delivered to a customer.

---

### Fields

| Field            | Required |
| ---------------- | -------- |
| Amount Delivered | Yes      |
| Recipient Name   | Yes      |
| Note             | No       |

### Business Rules

* Amount must be greater than zero.
* Recipient name required.
* Transaction recorded in history.
* Confirmation required before saving.

---

# 7. Settlement Module

## Description

Closes a customer's positive balance account.

### Business Rules

* Customer remains stored in database.
* Customer removed from active favorable-balance list.
* Full history remains accessible.
* Confirmation required.

---

# 8. Reporting Module

## PDF Export

The system shall allow:

* Downloading customer history as PDF.
* Downloading debt history.
* Downloading payment history.
* Downloading gold purchase history.

---

## Printing

The system shall allow:

* Printing customer history.
* Printing debt history.
* Printing gold purchase history.

---

# 9. Audit Requirements

Every financial operation must generate a historical record.

Operations that must be logged:

* Customer creation
* Debt registration
* Payment registration
* Debt settlement
* Gold purchase registration
* Money delivery
* Balance settlement

Each record should store:

* Transaction type
* Amount
* Notes
* Date and time
* User who performed the action

---

# 10. User Stories

### US-01 Create Customer

As an operator,
I want to create a customer,
So that I can manage future transactions.

---

### US-02 Search Customer

As an operator,
I want to search for a customer,
So that I can access their information quickly.

---

### US-03 Add Debt

As an operator,
I want to register a debt,
So that I can track money owed by a customer.

---

### US-04 Register Payment

As an operator,
I want to register a payment,
So that the customer's balance is updated.

---

### US-05 Settle Debt

As an operator,
I want to settle a debt,
So that the customer is removed from the debt list.

---

### US-06 Register Gold Purchase

As an operator,
I want to record a gold purchase,
So that the transaction becomes part of the customer history.

---

### US-07 Deliver Money

As an operator,
I want to register money delivered,
So that all financial movements are tracked.

---

### US-08 Generate PDF

As an operator,
I want to generate a PDF report,
So that I can share or archive transaction records.

---

### US-09 Print Reports

As an operator,
I want to print customer records,
So that physical copies can be maintained.

---

# 11. Non-Functional Requirements

### Performance

* Searches should return results in under 2 seconds.
* Dashboard metrics should load in under 3 seconds.

### Security

* Authenticated users only.
* Session validation required.
* SQL Injection protection.
* XSS protection.
* CSRF protection on forms.

### Data Integrity

* Financial records cannot be physically deleted.
* Historical records must remain immutable.
* All transactions must be timestamped.

### Availability

* System should support daily business operations.
* Automatic database backups recommended.

---

# 12. Out of Scope

The system shall NOT:

* Delete customer records permanently.
* Modify historical financial transactions after creation.
* Process online payments.
* Connect to banking systems.
* Manage inventory.
* Manage accounting taxes.

