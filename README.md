# SmartWasteHub – Group 9 BSSE-25 Coursework Project

## Project Overview

* **Institution:** BSSE-25, Group 9
* **Coursework Theme:** ICT Innovation & Entrepreneurship
* **Application Name:** SmartWasteHub
* **Problem Addressed:** Urban households, SMEs, and communities struggle with uncoordinated waste collection, inadequate recycling incentives, inconsistent pickup schedules, and lack of environmental impact visibility. Manual waste workflows lead to inefficiency, littering, and lost recycling opportunities.
* **Our Solution:** SmartWasteHub digitises community waste management with an integrated platform that enables households to request pickups, collectors to manage routes, and administrators to oversee environmental impact. Built-in recycling credits incentivise proper waste disposal.

## Why SmartWasteHub

* **Recycling Credit Incentives:** Households earn credits based on waste weight, encouraging sustainable habits.
* **Improved Waste Collection Efficiency:** Collectors receive clearly assigned pickup tasks, reducing missed collections.
* **Environmental Awareness:** Users see recycling impact through activity history and reward redemption data.
* **Inclusive Collaboration:** Role-based authentication for administrators, collectors, and households.

## Key Capabilities

* Household pickup requests.
* Real-time credit calculation based on waste weight.
* Collector dashboards for task management.
* Reward redemption system for households.
* Admin console for managing users, categories, and rewards.
* Pickup history, reward history, and credit statements.
* Clean, responsive UI with consistent theming.

## Tech Stack

* **Frontend:** PHP 8, HTML5, responsive CSS, Bootstrap.
* **Styling:** Clean white theme with green accents for environmental identity.
* **Backend:** PHP with MySQL database (`smartwastehub_db.sql`).
* **Assets:** Local `images/`, `css/`, and `js/` folders.
* **Tooling:** Git & GitHub for version control, XAMPP/LAMP stack for local deployment.

## Setup & Deployment

1. Clone the repository:

   ```bash
   git clone https://github.com/Elvis-1738/smartwastehub.git
   ```
2. Move the project folder into your Apache web root (`htdocs`).
3. Import the database file `smartwastehub_db.sql` using phpMyAdmin.
4. Update database credentials in:

   ```
   backend/config.php
   ```
5. Ensure all folders (`css/`, `js/`, `images/`) remain structured as provided.
6. Start Apache & MySQL in XAMPP.
7. Navigate to:

   ```
   http://localhost/smartwastehub/frontend/index.php
   ```

   to begin testing.

## Project Structure

```
smartwastehub/
├── backend/
│   ├── auth/
│   │   ├── login.php
│   │   ├── register.php
│   │   └── logout.php
│   ├── config.php
│
├── frontend/
│   ├── dashboard_household.php
│   ├── dashboard_collector.php
│   ├── dashboard_admin.php
│   ├── household_request_pickup.php
│   ├── household_pickup_history.php
│   ├── household_rewards_history.php
│   ├── household_redeem.php
│   ├── collector_pickups.php
│   ├── collector_complete_pickup.php
│   ├── collector_available.php
│   ├── admin_requests.php
│   ├── admin_rewards.php
│   ├── admin_users.php
│   ├── credit_statement.php
│   ├── index.php
│   └── includes/
│       ├── header.php
│       └── footer.php
├── css/
│   └── style.css
├── sql/
│   └── smartwastehub_db.sql
└── README.md
```

## SDLC Summary

| Phase                        | Key Activities                                                         | Artefacts                                  |
| ---------------------------- | ---------------------------------------------------------------------- | ------------------------------------------ |
| **1. Problem Discovery**     | Stakeholder interviews, waste workflow research, persona mapping       | Challenges brief, stakeholder matrix       |
| **2. Requirements Analysis** | Functional/NFR definition, backlog creation, risk assessment           | Requirements document, prioritised backlog |
| **3. System Design**         | Architecture, database schema, wireframes, UX flows                    | ER diagram, UI mockups, routing maps       |
| **4. Implementation**        | Role-based dashboards, collector workflows, credit engine, admin tools | Source code, sprint deliverables           |
| **5. Testing & Validation**  | Unit tests, integration tests, UI testing, stakeholder UAT             | Test report, UAT sign‑off, issue log       |
| **6. Deployment**            | Environment configuration, DB import, server validation                | Deployment checklist, release notes        |
| **7. Maintenance & Growth**  | Monitoring, bug fixes, feature roadmap                                 | KPI dashboard, enhancement roadmap         |

## Group Members

| Name                 | Registration Number | Student Number |
| -------------------- | ------------------- | -------------- |
| Asiimire Patricia    | 21/U/19271/PS       | 2100719271     |
| Kitonsa Elvis        | 20/U/7785/PS        | 2000707785     |
| Katuramu Edgar       | 22/U/21756/PS       | 2200721756     |
| Kizito Daniel Junior | 19/U/8282/EVE       | 1900708282     |

---

This README summarises the SmartWasteHub project for assessment and deployment, providing full setup instructions, system overview, and SDLC alignment.
A more in-depth study into the steps of the SDLC executed are included in the file sdlc.md 
