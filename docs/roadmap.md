# Yuppy AI Photobook Designer: Roadmap

Ten phases. Each phase leaves the app working, tested and deployable. Phases after 1 are plans and will be refined as we go.

| # | Phase | Goal | Key deliverables |
|---|---|---|---|
| 1 | **Foundation & Architecture** ✅ | A secure multi-role base to build on | Auth and roles, policies and owner filtering, studio/customer/branding profiles, reference data, private logo storage, BrandingResolver, audit trail, app shell and design system, Pest suite |
| 2 | **Design Engine** | A data model for book layouts | Spreads, pages, frames and slots as data; layout templates per design family and book format; editor library chosen (Konva or Fabric) behind an abstraction; projects can be created and configured (occasion, format, family, branding mode) |
| 3 | **Photo Uploads & Library** | Bring photos in safely | Chunked uploads to the private disk, image processing jobs (thumbnails, EXIF orientation, colour profile), per-project library, quotas, virus/type checks |
| 4 | **Book Editor** | Hands-on design | Drag and drop editor with auto-save, undo/redo, text blocks, branding placement, page management, autosave conflict handling |
| 5 | **AI Curation & Auto-Layout** | AI does the first draft | `ai_requests` in use: photo scoring (sharpness, faces, duplicates), story sequencing, auto-fill of a design family; provider adapter, cost tracking, human review |
| 6 | **Manufacturer Templates & Preflight** | Print-ready accuracy | Admin entry and verification of manufacturer specs (from official spec sheets only), bleed/safe-area/spine rules, DPI and preflight warnings |
| 7 | **Renderer** | Print files | Queued high-resolution PDF/JPEG rendering per verified template, proofs, render history |
| 8 | **Client Review & Collaboration** | Photographer ↔ client loop | Share links for clients, comments on spreads, approval workflow, notifications |
| 9 | **Orders & Payments** | Getting paid | Pricing, carts, checkout (e.g. M-Pesa and card), order states, invoices, manufacturer hand-off |
| 10 | **Operations & Scale** | Run it as a business | Admin user management (suspend, roles), reporting, backups, monitoring, performance, accessibility and security audit, production hardening |

## Rules that hold in every phase
- A photographer's books never carry Yuppy branding.
- Photographers and customers only ever see their own data; lists are owner-filtered by default.
- User files are private and are only served through authenticated, policy-checked routes.
- Manufacturer measurements are never invented; only verified spec-sheet values are used for print.
