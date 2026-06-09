### Core Business Model

The application is a multi-tenant system where each tenant owns a store and manages:

* Customers
* Products (gold, silver, metals, etc.)
* Transactions
* Debts and credits
* Reports
* Subscription

The main purpose is to track inventory and maintain a financial balance with each customer.

---

### Balance Logic

For each customer, there is a balance that can be in one of two states:

#### Customer has money in favor

Example:

1. Customer brings gold worth 50,000.
2. Store receives the gold.
3. Store does not pay immediately.

Result:

* Customer balance = +50,000
* Store owes customer 50,000

---

#### Customer owes the store

Example:

1. Customer previously had +50,000 in favor.
2. Customer later takes gold worth 70,000 from the store.

Result:

* 70,000 - 50,000 = 20,000
* Customer balance = -20,000
* Customer owes store 20,000

The balance should always be calculated automatically from all transactions.

---

### Transaction Directions

There are only two transaction directions:

#### Buy From Customer

Store receives products.

Effects:

* Stock increases.
* Store may owe money to customer.
* Customer can never owe money because of this transaction alone.

#### Sell To Customer

Store delivers products.

Effects:

* Stock decreases.
* Customer may owe money to store.
* Store cannot owe customer because of this transaction alone.

---

### Products

Products contain:

* Name
* Weight
* Initial value (optional)
* Stock quantity or weight
* In stock / Out of stock status

Requirements:

* Product autocomplete.
* Show current stock in dropdown.
* Show "Out of Stock" indicator.
* Allow out-of-stock products when buying from customer because stock is increasing.
* Prevent or warn when selling products with insufficient stock.

---

### Transactions

A transaction contains:

* Customer
* Direction
* Products
* Total value
* Amount paid now
* Remaining balance
* Payment status

Payment statuses:

* Settled
* Partial Payment
* Outstanding Balance

Features:

* Currency formatting.
* "Pay Total" button.
* Automatic debt calculations.
* One history record per transaction.

---

### Reports

Dashboard and reports should show:

* Total customers
* Total transactions
* Total inventory
* Outstanding receivables
* Outstanding payables
* Customer balances
* Purchase history
* Sales history
* Payment history
* Debt history
* Audit log

---

### Stores

Store configuration:

* Store name
* Store type
* Physical / Online / Both
* Active / Inactive

Store categories:

* Gold
* Silver
* Platinum
* Other metals

---

### Subscription

Still pending, but likely:

* Monthly
* Quarterly
* Semiannual
* Annual

with tenant-level billing.

---

## Questions

These are the most important questions I would clarify before continuing development:

### 1. What exactly is the balance sign?

Should the customer have a single balance value?

Example:

* +50,000 = Store owes customer.
* -20,000 = Customer owes store.

Or would you prefer two separate fields?

* Amount owed to customer.
* Amount customer owes store.

I strongly recommend a single running balance.

---

### 2. Can a transaction contain multiple products?

Example:

* Gold 18K
* Gold 24K
* Silver

all in the same transaction?

I assume yes.

---

### 3. Are products inventory items or unique pieces?

Example:

Option A:

* Gold 18K
* Weight = 150 grams

Inventory is aggregated.

Option B:

* Gold Ring #001
* Weight = 5 grams

Each item is unique.

This affects the entire inventory design.

---

### 4. When a customer brings gold worth 50,000, is the value entered manually or calculated?

Example:

* Weight = 10g
* Price per gram = 5,000

Should the system calculate 50,000 automatically?

Or should the tenant manually enter the value?

---

### 5. Can customers pay debts with products instead of cash?

Example:

Customer owes 20,000.

He brings gold worth 20,000.

Should that reduce the debt automatically?

This seems very likely in your business model.

---

### 6. What is the source of truth?

Should customer balances be:

* Calculated dynamically from all transactions every time

or

* Stored in a balance table and updated after each transaction

For performance and reporting, I recommend storing the current balance and also keeping all transaction history for auditing.

These answers will define the database structure, transaction logic, inventory model, and reporting system.
