# SmartWasteHub SDLC Report – Group 9 BSSE-25

## Project Overview

* **Vision:** Deliver a sustainability-focused waste management and recycling incentive platform that empowers households, SMEs, and collectors through efficient scheduling, transparent credit rewards, and environmental impact reporting.
* **Team Footprint:** 4 core engineers (frontend, backend, data, QA) with collaborative input from innovation and sustainability advisors.
* **Lifecycle Model:** Hybrid Agile – structured discovery and design followed by incremental implementation in sprints.
* **Solution Architecture:** Layered PHP application across `frontend/`, `backend/`, and `admin/` modules, backed by a MySQL database structured for efficient querying and audit-ready logs.

---

## Group Members

| Name                 | Registration Number | Student Number |
| -------------------- | ------------------- | -------------- |
| Asiimire Patricia    | 21/U/19271/PS       | 2100719271     |
| Kitonsa Elvis        | 20/U/7785/PS        | 2000707785     |
| Katuramu Edgar       | 22/U/21756/PS       | 2200721756     |
| Kizito Daniel Junior | 19/U/8282/EVE       | 1900708282     |

---

## Phase 1 – Problem Discovery & Initiation

* **Objectives:** Understand waste management challenges, validate community needs, identify stakeholder pain points.
* **Key Inputs:** Household interviews, community surveys, environmental sustainability frameworks (SDG 11 & 12).
* **Core Activities:**

  * Engaged 20 households, 6 collectors, and 3 municipal representatives.
  * Benchmarked inefficiencies in current manual waste collection workflows.
  * Conducted design thinking sessions to map user journeys and opportunity gaps.
* **Deliverables:** Stakeholder matrix, problem statement, project charter, opportunity assessment.
* **Risks & Mitigation:**

  * *Low adoption by households* → Introduce credit reward incentives.
  * *Collector coordination issues* → Implement digital assignment dashboard.
* **Metrics:** Persona coverage ≥90%, validated opportunity with measurable improvements.

---

## Phase 2 – Requirements Analysis

* **Objectives:** Convert findings into requirements, refine scope, and define success criteria.
* **Activities:**

  * Documented functional requirements (pickup workflows, credit rewards, redemption, dashboards).
  * NFRs (security, responsiveness, uptime, usability) captured and prioritised.
  * Data policies, audit logs, and security guidelines established.
* **Artefacts:** Requirements specification, refined backlog, acceptance criteria, risk register.
* **Risks & Mitigation:** Scope creep avoided via strict change control.
* **Metrics:** ≥95% clarity in user stories and acceptance criteria.

---

## Phase 3 – System Design

* **Objectives:** Establish architecture, data flow, interfaces, and UI/UX structures.
* **Design Highlights:**

  * **Architecture:** Three-tier separation with clear routing and access control.
  * **Database:** ERD including `users`, `pickup_requests`, `waste_categories`, `reward_wallets`, `reward_items`, and `reward_transactions`.
  * **Integration Points:** SMS/WhatsApp notification hooks (future).
  * **UI/UX:** Wireframes for dashboards, pickup forms, collector tools.
* **Deliverables:** ERD, interface diagrams, wireframes, design system tokens.
* **Metrics:** Full mapping of features to design artefacts.

---

## Phase 4 – Implementation (Development Sprints)

* **Objectives:** Build and integrate features through structured sprints.
* **Sprints:**

  * *Sprint 0:* Environment setup, repo structure.
  * *Sprint 1:* Authentication, household dashboards.
  * *Sprint 2:* Pickup requests, collector dashboards, completion flow.
  * *Sprint 3:* Reward engine, credit redemption, admin tooling.
* **Practices:** PSR-12 coding standards, peer reviews, Git workflow, reusable components.
* **Tools:** VS Code, GitHub, XAMPP, Trello.
* **Metrics:** Sprint completion ≥80%, reviewed and tested PRs.

---

## Phase 5 – Testing & Quality Assurance

* **Objectives:** Verify correctness, usability, and stability.
* **Testing Types:**

  * Unit testing for credit logic.
  * Integration tests for pickup-to-credit pipeline.
  * UI tests for responsiveness and accessibility.
  * Database validation and query optimisation.
* **Defect Workflow:** Logged in GitHub issues, triaged, and resolved.
* **Exit Criteria:** 0 critical defects, UAT sign-off.
* **Metrics:** Page load <2s on local environment.

---

## Phase 6 – Deployment & Release Management

* **Objectives:** Package, deploy, and validate system.
* **Environment Strategy:** Local XAMPP → Demo Environment.
* **Deployment Pipeline:**

  1. Merge to main.
  2. Database import and config update.
  3. Smoke testing and rollback validation.
* **Deliverables:** Release notes, deployment checklist.
* **Risks:** Configuration drift mitigated via documented setup.

---

## Phase 7 – Maintenance, Monitoring & Continuous Improvement

* **Objectives:** Enhance functionality and maintain performance.
* **Activities:**

  * Monitor KPIs (credits issued, pickups completed).
  * Bug fixes and interface improvements.
  * Plan extended analytics and mobile support.
* **Metrics:** Platform stability ≥99%, growing adoption.

---

## Entrepreneurship & Innovation Framework

* **Value Proposition:** Efficient waste collection, credit-based recycling incentives, sustainability impact tracking.
* **Customer Segments:** Households, SMEs, collectors, municipalities.
* **Revenue Streams:** Partnerships, premium analytics features (future).
* **Impact:** Waste reduction, incentivised recycling, improved urban hygiene.

---

## Integrated Timeline

| Week | Milestone    | Deliverables              |
| ---- | ------------ | ------------------------- |
| 1    | Discovery    | Interviews, charter       |
| 2    | Requirements | Backlog, risk register    |
| 3    | Design       | ERD, wireframes           |
| 4    | Sprint 1     | Auth, household dashboard |
| 5    | Sprint 2     | Pickup workflows          |
| 6    | Sprint 3     | Rewards, admin tools      |
| 7    | QA & UAT     | Test report, sign-off     |
| 8    | Deployment   | Release v1.0, demo        |

---

This SDLC report documents the full engineering lifecycle for SmartWasteHub, demonstrating structured execution, innovation alignment, and engineering rigour.
