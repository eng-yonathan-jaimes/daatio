### 📄 DOCUMENT 1: Global Application Architecture & Authentication

#### 1.1 Authentication Layer (Iniciar Sesión)

* **Login Form Requirements**: The application must require a secure login dashboard featuring a designated Username (`Usuario`) input and a Password (`Contraseña`) field.
* **Password Recovery**: A distinct workflow option for password recovery (`Recuperar contraseña`) must be present on the login screen to assist users who forget their credentials.
* **Default Setup State**: Wireframes illustrate a default admin view (`admin`) for immediate platform configuration access.

#### 1.2 Layout & Global Navigation

* **Post-Login Routing Hub**: Upon a successful authentication event, the system redirects users to a dark-themed route selection panel prompting them to select a module to proceed.
* **Module Selection Triggers**: The workspace landing page features two primary entry points: a "Clientes" (Clients) option and a "Compra de Oro" (Gold Purchasing) option.
* **Collapsible Sidebar Menu**: A global hamburger menu button must persist on the top-left section of the interior layouts. Activating this element reveals a sliding drawer menu mapping out the complete layout:
* Current Module Reference Indicator / Logo (`Lubricantes La 27`).
* Link to Client Management Module (`Clientes`).
* Link to Gold Purchase Management Module (`Compra de Oro`).
* Link to System Settings (`Configuración`).
* Logout Command Execution Action (`Cerrar sesión` / Exit icon).



---

### 📄 DOCUMENT 2: Module "Clientes" Specification (Debtor Management)

#### 2.1 Module Dashboard Views & State Aggregations

* **Key KPI Cards**: The main interface displays four statistical KPI cards aggregating the financial status of active accounts:
* **Total Clients (`Total clientes`)**: Integer count of all registered entries.
* **Total Debt (`Deuda total`)**: Monitored compilation of outstanding debts owed by clients to the establishment.
* **Total Paid (`Total pagado`)**: Summing up historic tracking data for closed or partially paid amounts.
* **Pending Balance (`Saldo pendiente`)**: Calculated state indicating real-time aggregate exposure.


* **Client Search Query Filter**: A single mid-page input selector (`Buscar cliente por nombre...`) executes dynamic substring filtering across the datagrid rows.
* **Debtor Data Table Layout**: The table lists active entries matching data definitions with columns for:
* *Client Name (`Nombre`)*
* *Phone Contact (`Teléfono`)*
* *Current Outstanding Debt (`Deuda pendiente`)*
* *Action Indicators*: Detail View (Eye Icon) and Liquidate/Clear (Trash Bin Icon).



#### 2.2 Client Ingestion Workflow (Agregar Cliente Modal)

* **Trigger Placement**: Positioned as an accent command button (`+ Nuevo cliente`) near the top right quadrant of the interface.
* **Input Fields Schema**:
* Full Name (`Nombre completo`) — Text input, required field.
* Phone Number (`Teléfono`) — Optional alphanumeric string input.
* Initial Debt Configuration Toggle (`Deuda inicial`) — Activates input for entering an initial debt balance.
* Amount (`Monto de la deuda`) — Numeric currency entry defaulting to 0.



#### 2.3 Individual Ledger & Deep-Dive Workspace (Vista Detalle)

Clicking the eye icon opens a client workspace focusing on an individual account ledger.

* **Header Indicators**: Displays Client Identity Details alongside global export operations: Download PDF Summary (`Descargar PDF`) and Print Ledger Data (`Imprimir`).
* **Profile Aggregation KPIs**: Client-scoped indicators summarizing `Deuda total`, `Total pagado`, and `Saldo pendiente`.
* **Active Debts Section (`Deudas activas`)**: Datagrid component reflecting specific items financed over time. Displays properties including item label (e.g., `ACEITE`), timestamp logging, full liability value, amount cleared, and remaining balance.
* **Installment History Lineage (`Historial de abonos`)**: A collapsible section detailing payments processed against specific debt items.

#### 2.4 Transactional Ledger Mutations (Modals)

* **Add Liability Entry Modal (`+ Agregar deuda`)**: Used when an existing client requests credit again. Requires a Debt Amount (`Monto de la deuda`) field and an optional Concept input (`Concepto`).
* **Process Installment Payment Modal (`Abonar`)**: Captures standard payment values against active rows. Contains a Payment Amount field (`Monto del abono`) and an optional text block for tracking context (`Notas`).

#### 2.5 Debt Liquidation Rules (`Liquidar`)

* **Trigger Event**: Fired via action panels or the main list row context menus.
* **Destructive Confirmation Text**: A modal alerts users that liquidating permanent history will delete old account records: *"Al liquidar la deuda '[Concepto]', todos sus registros y abonos serán eliminados permanentemente."*.
* **Soft-Purge State Rule**: When confirmed, the client entity must not be hard deleted from the application's base directory tables. Instead, the entity remains in the global database record but is soft-purged from the active debtor dashboard visibility tracking lists.

---

### 📄 DOCUMENT 3: Module "Compra de Oro" Specification (Gold Acquisition Ledger)

#### 3.1 Module Dashboard Views & State Aggregations

This dashboard maps metrics from commercial operations where clients retain balances in their favor based on gold deposits.

* **Key KPI Cards**: The layout contains four core metric analytics cards:
* **Active Gold Clients (`Clientes activos`)**: Operational count of suppliers holding current records.
* **Total Disbursed (`Total entregado`)**: Aggregates cash payouts drawn from active accounts.
* **Global Valuation Holding (`Saldo total`)**: Total value of holdings maintained within system environments.
* **Accumulated Vault Mass (`Peso total`)**: Metric tracking total physical mass inputs in grams across open lots.


* **Search Context Filter**: Input bar matching layout definitions to isolate specific entities by name.
* **Supplier Data Table Layout**: Displays clients with a credit balance in their favor, listing columns for:
* *Supplier Name (`Nombre`)*
* *Phone Vector (`Teléfono`)*
* *Balance in Favor (`Saldo a favor`)*
* *Action Indicators*: Detail View (Eye Icon).



#### 3.2 Supplier Ingestion Workspace (Agregar Cliente de Oro Modal)

* **Trigger Accent Action**: Fired via the corresponding interface command button (`+ Nuevo cliente`).
* **Flexible Validation Properties**: Every form capture entry field within this container is designated as functionally optional, adjusting to zero states if empty:
* Full Name (`Nombre completo`)
* Phone Number (`Teléfono`)
* Gold Net Weight (`Peso del oro (g)`)
* Unit Spot Price Matrix Valuation (`Precio por gramo (COP/g)`)
* Total Value Disbursed (`Valor total pagado (COP)`)



#### 3.3 Vault Portfolio Ledger Workspace (Vista Detalle)

* **Header Operations**: Features tools to print logs or output reports via PDF, alongside action layout fields for `Saldo a favor`, `Total depositado`, `Total entregado`, and `Peso acumulado`.
* **Dual Lineage Components**:
* **Deposits Register (`Depósitos`)**: Documents inbound structural lots including descriptive properties, gram weight metrics, recorded rate values, final amounts, and creation timestamps.
* **Cash Drawdowns Register (`Entregas de dinero`)**: Logs physical cash disbursements issued against the supplier's running credit balance.



#### 3.4 Operational Activity Commands

* **Cash Disbursement Order (`Entregar dinero`)**: Opened inside individual client views. Displays available account balances and fields to specify the withdrawal amount (`Monto a entregar`), name of the proxy or receiver (`Entregado a`), and tracking metadata notes (`Notas`).
* **Gold Balance Liquidation Event (`Liquidar`)**: Launches a clear confirmation step. The target entity must remain stored securely inside base lookups to preserve operational trace values, while being excluded from open balance sheets.

---

### 📄 DOCUMENT 4: Universal Form Validation & AI User Stories

#### 4.1 System-Wide Guardrails (Reglas Críticas)

* **Omnipresent Action Confirmation Steps**: Any state changes triggered by submitting or leaving open screens (*Guardar*, *Cancelar*, *Aceptar*, *Registrar*, or *Liquidar*) must intercept execution with a native system prompt dialogue.
* **Prompt Copy Mappings**:
* On leaving or cancelling: *"¿Está seguro de cancelar? Sí / No"*.
* On saving modifications: *"¿Está seguro de guardar? Sí / No"*.



---

#### 4.2 Product Backlog: Functional User Stories

##### **USER STORY 1: Global Security Guardrails & Navigation Layout**

* **As an** Administrative Agent
* **I want to** authenticate securely and navigate via a centralized sliding sidebar
* **So that** I can change system contexts efficiently between client debt tracking and gold trading operations without losing session integrity.
* **Acceptance Criteria**:
* *Scenario 1: Authenticated Routing Context*
* Given an active user session inside the app, when the user triggers the sidebar toggle icon on the top left, then a drawer reveals containing navigation destinations for `Clientes`, `Compra de Oro`, `Configuración`, and a clear Logout hook.


* *Scenario 2: Dynamic Identity Placement*
* Given rendering active route paths, the system side drawer must show explicit branding referencing `Lubricantes La 27` and display active role indicator references (`admin`).





##### **USER STORY 2: Dynamic Client Debtor Ledger Aggregations**

* **As a** Financial Auditor
* **I want to** inspect aggregate debt statistics through dynamic dashboard metrics cards
* **So that** I can accurately assess overall credit risk exposure instantly.
* **Acceptance Criteria**:
* *Scenario 1: Analytical Math Dependencies*
* Given variations in database rows under client metrics, the platform updates metric fields for `Total clientes`, `Deuda total`, `Total pagado`, and `Saldo pendiente` dynamically without needing page reloads.


* *Scenario 2: Client Filtering Execution*
* Given row listings displaying name profiles, when typing search characters into the `Buscar cliente por nombre...` filter field, the UI updates instantly to match name patterns using text substring filters.





##### **USER STORY 3: Multi-Conditional Account Liquidation**

* **As a** Store Operator
* **I want to** soft-purge active ledger entries using the system's liquidation tools
* **So that** completed or resolved history accounts do not clutter operational workspace grids while remaining intact for historical audit reporting.
* **Acceptance Criteria**:
* *Scenario 1: Destructive Intercept Triggers*
* Given an intention to isolate finished balance states, when selecting the `Liquidar` transaction action, an intercept dialogue box must pop up asking for confirmation and warning of permanent history removal.


* *Scenario 2: Soft-Purge Index Exclusion*
* Given a user confirms the account liquidation action, then the system updates flag parameters to exclude that customer from active dashboards while keeping their foundational audit records in database tables.