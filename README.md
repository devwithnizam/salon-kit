# SalonKit — Booking System for WordPress + Elementor

> **Version:** 2.0.0  
> **Requires:** WordPress 5.8+, PHP 7.4+, Elementor 3.24+  
> **License:** GPL v2 or later  
> **Author:** MD Nizam Uddin

---

## Table of Contents

1. [What is SalonKit?](#what-is-salonkit)
2. [Features](#features)
3. [Installation](#installation)
4. [Quick Start](#quick-start)
5. [Creating Services](#creating-services)
6. [Creating Professionals](#creating-professionals)
7. [Assigning Professionals to Services](#assigning-professionals-to-services)
8. [Setting Weekly Schedules](#setting-weekly-schedules)
9. [Displaying the Booking Form](#displaying-the-booking-form)
   - [Method 1: Elementor Widget (Recommended)](#method-1-elementor-widget-recommended)
   - [Method 2: Shortcode](#method-2-shortcode)
10. [Customizing Everything via Elementor](#customizing-everything-via-elementor)
    - [Text Labels](#text-labels)
    - [Visibility (Show / Hide)](#visibility-show--hide)
    - [Icons](#icons)
    - [Colors](#colors)
    - [Typography](#typography)
    - [Layout & Spacing](#layout--spacing)
11. [Services Grid Widget](#services-grid-widget)
12. [Managing Bookings](#managing-bookings)
13. [Shortcode Reference](#shortcode-reference)
14. [Developer Docs](#developer-docs)
    - [File Structure](#file-structure)
    - [CSS Custom Properties](#css-custom-properties)
    - [JavaScript API](#javascript-api)
    - [Hooks & Filters](#hooks--filters)
    - [Data Attributes Reference](#data-attributes-reference)
15. [Frequently Asked Questions](#frequently-asked-questions)
16. [Troubleshooting](#troubleshooting)
17. [Changelog](#changelog)

---

## What is SalonKit?

SalonKit is a complete salon appointment booking system for WordPress. It allows your customers to book services through a beautiful 5-step wizard directly on your site.

**The booking flow:**
1. **Service** — Customer picks a service (e.g. Haircut, Massage, Manicure)
2. **Professional** — Customer picks which staff member they want
3. **Date** — Customer picks a date from an interactive calendar
4. **Time** — Customer picks an available time slot
5. **Details** — Customer enters name, email, phone & notes → Confirms

---

## Features

### Core Features
- **5-step booking wizard** — Clean, modern, mobile-friendly
- **Services management** — Add services with price, duration, and slot quantity
- **Professionals management** — Add team members with photos, bios
- **Weekly schedules** — Set working hours per professional (including lunch breaks)
- **Smart time slots** — Auto-generated based on service duration & professional schedule
- **Overbooking prevention** — MySQL transaction locking prevents double-booking
- **Email confirmations** — Automatic email sent to customer on booking
- **Custom database table** — Fast queries, no bloat on WordPress posts table

### Customization Features (Elementor)
- **Edit every text** — All labels, buttons, placeholders, messages are editable
- **Show/hide anything** — Toggle visibility of every element (steps, fields, images, prices)
- **Full color control** — 30+ color settings mapped to brand colors
- **Typography control** — 16 typography levels with full Google Fonts support
- **Layout control** — Spacing, padding, border radius, shadows for every component
- **Icon picker** — Choose from 22 custom SalonKit icons or any FontAwesome icon
- **Per-widget styling** — Each form instance can have its own colors & settings
- **Brand colors auto-selected** — Pulls from Elementor Global Colors if set

### Design Features
- **Modern indigo palette** — Clean, trust-building, unisex design
- **Fully responsive** — 4 breakpoints (desktop, tablet, phone, tiny phone)
- **Skeleton loaders** — Animated loading states instead of text
- **Micro-interactions** — Smooth transitions, hover effects, focus states
- **Keyboard accessible** — Full keyboard navigation (Tab, Enter, Escape)
- **Reduced motion support** — Respects `prefers-reduced-motion`
- **40px+ touch targets** — Mobile-friendly tap areas

---

## Installation

1. **Download** the plugin zip file
2. **WordPress Admin → Plugins → Add New → Upload Plugin**
3. Choose the zip file and click **Install Now**
4. Click **Activate**
5. The plugin will automatically:
   - Create 3 Custom Post Types: `Services`, `Professionals`, `Bookings`
   - Create a custom database table for bookings
   - Register 2 Elementor widgets
   - Register the `[salon_booking]` shortcode

---

## Quick Start

1. Go to **SalonKit → Services** → **Add Service** → Create "Haircut" ($45, 45 min)
2. Go to **SalonKit → Professionals** → **Add Professional** → Create "Jane"
3. Go back to **Services** → Edit "Haircut" → Check "Jane" under **Assign Professionals**
4. Go to **Professionals** → Edit "Jane" → Set her **Weekly Schedule**
5. Edit a page with **Elementor** → Search for **"Salon Booking Form"** widget
6. **Publish** — you're done!

---

## Creating Services

Navigate to **SalonKit → Services** in the WordPress admin menu.

### Service Fields

| Field | Description | Example |
|---|---|---|
| **Title** | Service name displayed to customers | "Classic Haircut" |
| **Description** | Short description (excerpt) | "Includes wash, cut & style" |
| **Featured Image** | Thumbnail shown on service cards | 300×300 px recommended |
| **Price ($)** | Dollar amount | 45.00 |
| **Duration (minutes)** | How long the service takes | 45 |
| **Slot Quantity** | Max clients per time slot | 2 (means 2 people can book the same slot) |

> **Tip:** Slot Quantity of 2+ allows parties/couples to book together at the same time.

---

## Creating Professionals

Navigate to **SalonKit → Professionals** (under Services).

### Professional Fields

| Field | Description |
|---|---|
| **Title** | Professional's display name |
| **Description** | Full bio shown in admin |
| **Excerpt** | Short bio shown on the booking card |
| **Featured Image** | Profile photo (square recommended, 300×300 px) |

---

## Assigning Professionals to Services

Professionals must be assigned to services, otherwise no one will appear for that service.

### Method A: From the Service
1. Edit a **Service**
2. In the **"Assign Professionals"** meta box (right sidebar), check the professionals who offer this service
3. **Update/Save**

### Method B: From the Professional
1. Edit a **Professional**
2. In the **"Assigned Services"** meta box (right sidebar), check the services they offer
3. **Update/Save**

> The assignments are bi-directional — updating from either side syncs automatically.

---

## Setting Weekly Schedules

Each professional needs a weekly schedule so the system knows when to generate time slots.

1. Edit a **Professional**
2. Scroll to the **"Weekly Schedule"** meta box
3. For each day of the week:
   - Check **"Works?"** to mark as a working day
   - Set **Start** and **End** time (e.g. 09:00 — 17:00)
   - Optionally set **Lunch Start** and **Lunch End** for a break (e.g. 12:00 — 13:00)
4. **Update/Save**

> **Note:** Sundays are disabled by default in the booking calendar (can be changed via customization).

---

## Displaying the Booking Form

### Method 1: Elementor Widget (Recommended)

1. Edit a page/post with **Elementor**
2. Search for **"Salon Booking Form"** in the widget panel
3. Drag it onto your page
4. **All settings** are available in the Elementor panel under:
   - **Content → Text Labels** — Edit any text
   - **Content → Visibility** — Show/hide elements
   - **Content → Icons** — Choose icons
   - **Style → Colors** — 30+ color controls
   - **Style → Typography** — 16 font controls
   - **Style → Layout & Spacing** — Sizing & padding

### Method 2: Shortcode

```php
[salon_booking]
```

Place this shortcode on any page or post. The form will render with default settings.

> **Shortcode Tip:** You cannot override individual text/color values via shortcode attributes. For customization, use the Elementor widget.

---

## Customizing Everything via Elementor

When you select the **Salon Booking Form** widget in Elementor, you'll find these sections in the panel:

### Text Labels

Every single text string is editable:

| Section | Controls |
|---|---|
| **Step Titles** | Panel headings for all 5 steps |
| **Button Labels** | Next, Back, Submit, Book Again |
| **Step Labels** | The small labels above step numbers |
| **Summary Defaults** | "No service selected" etc. |
| **Form Fields** | Labels & placeholders for name, email, phone, notes |
| **Booking Summary Labels** | Service, Professional, Date, Time, Price |
| **Messages** | Loading, empty, error, submitting, slot status |

### Visibility (Show / Hide)

Toggle every major element:

- Step Indicator (the numbered circles)
- Summary Bar
- Individual steps (1–5)
- Success screen
- Form fields (name, email, phone, notes)
- Price, duration, description, images on service cards
- Professional photos
- Remaining slot count
- Booking summary box on step 5
- Name/email required toggle

### Icons

Choose custom icons for:
- Summary bar items (service, professional, date, time)
- Navigation arrows (back, next)
- Calendar navigation
- Success checkmark

Uses the **SalonKit** icon set by default. Change to any FontAwesome icon.

### Colors

30+ color controls organized by function:

| Group | Controls |
|---|---|
| **Primary** | Primary, Hover, Light, Accent, Accent Soft |
| **Surface** | Form Background, Card Background, Card Border |
| **Text** | Body Text, Muted Text, Field Label |
| **Cards** | Card Active Background, Card Active Text |
| **Inputs** | Input Background, Input Border, Input Focus Border |
| **Buttons** | Primary Button (bg, text, hover), Back Button (bg, text, hover) |
| **Calendar** | Today Border, Step Done Background |
| **States** | Success Icon, Error Text, Slot Full |
| **Effects** | Shadow Color, Summary Background |

> **Pro Tip:** If you've set Elementor Global Colors, those values auto-populate as defaults. Override per-widget as needed.

### Typography

Full typography control for every text level using Elementor's Group_Control_Typography:

| Control | Applies To |
|---|---|
| Panel Title | Step headings |
| Step Label | Labels above number circles |
| Step Number | The circled numbers (1–5) |
| Service Name | Service card titles |
| Service Price | Price display |
| Service Description | Description text |
| Professional Name | Professional card names |
| Professional Bio | Short biography |
| Summary Text | Summary bar items |
| Field Label | Form input labels |
| Field Input | Input & textarea text |
| Button Text | All buttons |
| Success Title | "You're all booked!" |
| Calendar Month | Month/year label |
| Time Slot | Time display in grid |

Each control includes: Font Family, Size, Weight, Transform, Style, Decoration, Line Height, Letter Spacing.

> Fonts are loaded from Google Fonts automatically. You can select any Google Font.

### Layout & Spacing

| Control | Description |
|---|---|
| Form Max Width | Overall form container width |
| Form Padding | Inner spacing of form |
| Form Border Radius | How rounded the form corners are |
| Form Border Width | Form outline thickness |
| Form Shadow | Box shadow preset (none/sm/md/lg/xl) |
| Card Grid Gap | Spacing between cards |
| Card Padding | Inner spacing of cards |
| Card Border Radius | Card corner roundness |
| Card Border Width | Card outline thickness |
| Card Hover Shadow | Shadow on hover (none/sm/md/lg) |
| Button Height | Vertical size of buttons |
| Button Border Radius | Button corner roundness |
| Button Padding | Button inner spacing |
| Input Height | Text field vertical size |
| Input Border Radius | Input corner roundness |
| Summary Padding | Summary bar inner spacing |
| Step Circle Size | Diameter of step number circles |

All size controls are **responsive-aware** — set different values for desktop, tablet, and mobile.

---

## Services Grid Widget

A second widget — **Salon Services Grid** — displays your services in a card grid layout (great for service pages).

### Content Controls

| Control | Description |
|---|---|
| Columns | 1–4 columns |
| Mobile Columns | Override for phone screens |
| Show Price | Toggle price display |
| Show Duration | Toggle duration display |
| Show Description | Toggle description text |
| Show Image | Toggle featured image |
| Max Services | Limit number shown (-1 = all) |
| Order By | Title, Date, Random, Menu Order |
| Order Direction | Ascending/Descending |

### Style Controls

| Section | Controls |
|---|---|
| **Card Style** | Background, Border color/width, Border radius, Shadow preset, Hover effect, Image height/fit |
| **Typography** | Name (font + color), Description (font + color), Price (font + color), Duration (font + color) |
| **Layout & Spacing** | Grid gap, Body padding, Footer padding |

---

## Managing Bookings

### Viewing Bookings

Navigate to **SalonKit → Bookings** (under Services). You'll see a table with:
- Booking ID
- Client name & email
- Service name
- Professional name
- Date & time
- Price & status

### Dashboard Widget

A **"Today's Bookings"** widget appears on the WordPress Dashboard showing all confirmed appointments for the current day.

### Booking Storage

Bookings are stored in two places:
1. **Custom database table** (`wp_salon_bookings`) — used for slot availability queries
2. **Custom post type** (`salon_booking`) — used for admin display & management

---

## Shortcode Reference

```
[salon_booking]
```

- **No attributes** currently supported — all customization goes through the Elementor widget
- **Outputs** the full booking form with default styles & settings
- **Enqueues** CSS & JS automatically

---

## Developer Docs

### File Structure

```
salon-kit/
├── salon-kit.php                          # Plugin bootstrap
├── assets/
│   ├── css/
│   │   └── salon-kit.css                  # All styles (~1000 lines)
│   ├── icons/
│   │   └── salonkit-icons.json            # Elementor icon definitions
│   └── js/
│       └── salon-kit.js                   # Vanilla JS (~680 lines)
├── includes/
│   ├── class-admin.php                    # Admin columns & dashboard widget
│   ├── class-ajax.php                     # AJAX endpoints (4 handlers)
│   ├── class-bookings-db.php              # Custom DB table CRUD
│   ├── class-cpt.php                      # 3 Custom Post Types
│   ├── class-frontend-assets.php          # CSS/JS registration + localization
│   ├── class-icons.php                    # Elementor icon set registration
│   ├── class-meta-boxes.php               # Service & Professional meta boxes
│   └── class-slot-engine.php              # Slot generation & availability
├── templates/
│   └── booking-form.php                   # HTML template (data-attribute driven)
└── widgets/
    ├── class-booking-widget.php           # Main booking form Elementor widget
    ├── class-services-widget.php           # Services grid Elementor widget
    └── traits/
        ├── trait-text-controls.php         # 40+ text controls
        ├── trait-visibility-controls.php   # 20 toggle controls
        ├── trait-color-controls.php        # 30 color controls
        ├── trait-typography-controls.php   # 16 typography controls
        ├── trait-spacing-controls.php      # 18 layout controls
        └── trait-icon-controls.php         # 12 icon picker controls
```

### CSS Custom Properties

All colors and key values are CSS custom properties set on `.sb-wrap`. Elementor overrides these per-widget instance.

```css
.sb-wrap {
  --sk-primary:         #6366f1;
  --sk-primary-hover:   #4f46e5;
  --sk-primary-lite:    #eef2ff;
  --sk-accent:          #f59e0b;
  --sk-accent-soft:     #fef3c7;
  --sk-bg:              #ffffff;
  --sk-text:            #0f172a;
  --sk-text-muted:      #64748b;
  --sk-border:          #e2e8f0;
  --sk-card-bg:         #ffffff;
  --sk-card-border:     #e2e8f0;
  --sk-card-active-bg:  #6366f1;
  --sk-card-active-text:#ffffff;
  --sk-input-bg:        #ffffff;
  --sk-input-border:    #e2e8f0;
  --sk-input-focus:     #6366f1;
  --sk-btn-primary-bg:  #6366f1;
  --sk-btn-primary-text:#ffffff;
  --sk-btn-primary-hover:#4f46e5;
  --sk-btn-back-bg:     #f1f5f9;
  --sk-btn-back-text:   #475569;
  --sk-btn-back-hover:  #e2e8f0;
  --sk-success-icon:    #10b981;
  --sk-error:           #ef4444;
  --sk-summary-bg:      #f8fafc;
  --sk-label:           #0f172a;
  --sk-shadow-color:    rgba(99,102,241,0.12);
  --sk-step-done:       #6366f1;
  --sk-today-border:    #f59e0b;
  --sk-slot-full:       #ef4444;
  --sk-slot-full-bg:    #fef2f2;
  --sk-radius:          6px;
  --sk-radius-lg:       10px;
}
```

Override any variable via regular CSS to globally change styles.

### JavaScript API

The booking form JS is vanilla JavaScript (no jQuery dependency). Key structure:

```
SalonKit (global from wp_localize_script)
├── ajax_url   — WordPress admin-ajax URL
├── nonce      — Security nonce
└── services   — Array of service objects

Internal state object:
  state.step, state.service, state.professional,
  state.date, state.time, state.isSubmitting
```

**AJAX Endpoints** (all registered in `class-ajax.php`):

| Action | Input | Returns |
|---|---|---|
| `salon_get_services` | — | All services with price/duration/thumbnail |
| `salon_get_professionals` | `service_id` | Professionals for a service |
| `salon_get_slots` | `professional_id, service_id, date` | Available time slots with remaining count |
| `salon_save_booking` | All booking data + nonce | Confirmation or error |

### Hooks & Filters

| Hook | Type | File | Description |
|---|---|---|---|
| `salon_booking_nonce` | filter | `class-ajax.php` | Nonce action name (default: `salon_booking_nonce`) |
| `sk_slots_{$pro}_{$date}` | transient | `class-slot-engine.php` | Cached slot data (1 hour) |

**WordPress standard filters that apply:**
- `the_excerpt` — on service/professional descriptions in frontend
- `post_thumbnail_html` — on service/professional images

### Data Attributes Reference

The template uses `data-*` attributes for all dynamic content and visibility:

```html
<!-- Text content (JS reads data-sk-text, writes to .textContent) -->
<span data-sk-text="step1_title">Choose a Service</span>

<!-- Visibility (JS reads data-sk-vis, sets display:none when value is not 'yes') -->
<div data-sk-vis="show-field-name">...</div>

<!-- Placeholder text -->
<input data-sk-text="field_name_placeholder" placeholder="Jane Smith">
```

**How the text system works:**
1. Widget/shortcode outputs all text as `data-sk-text-*` attributes on `.sb-wrap`
2. JS reads `.sb-wrap.dataset` to get values
3. JS finds all `[data-sk-text]` elements and populates them
4. For inputs/textarea, it sets `.placeholder` instead of `.textContent`

**How the visibility system works:**
1. Widget/shortcode outputs all visibility toggles as `data-show-*` attributes on `.sb-wrap`
2. JS reads `.sb-wrap.dataset` to check if element should be visible
3. JS finds all `[data-sk-vis]` elements and sets `display:none` if the corresponding toggle is not `'yes'`

---

## Frequently Asked Questions

**Q: Can I use this without Elementor?**  
A: Yes! Use the `[salon_booking]` shortcode. You won't have the Elementor customization panel, but the form works with default settings.

**Q: Can I customize the email that goes to customers?**  
A: Currently the email is a simple hardcoded template. You can customize it in `includes/class-ajax.php` (lines ~149-152). A future version will include email template controls.

**Q: Can customers cancel their own bookings?**  
A: Not yet. Cancellations must be done from the WordPress admin (SalonKit → Bookings). Self-service cancellation is planned.

**Q: How do I change the currency symbol?**  
A: Prices are stored as numbers. The `$` prefix is hardcoded in the template and JS. To change it, edit `booking-form.php` and `salon-kit.js` replacing `$` with your currency symbol. A future version will have a currency setting.

**Q: Can I have multiple booking forms on one page?**  
A: Yes, multiple instances work. Each widget instance has its own data attributes and styles. However, sharing state between forms on the same page is not supported.

**Q: What happens if two customers book the same slot at the same time?**  
A: The plugin uses MySQL `SELECT ... FOR UPDATE` within a transaction to lock the row while checking availability. Only the first customer gets the slot; the second gets an error message.

**Q: Are booking slots cached?**  
A: Yes, available slots are cached via WordPress transients for 1 hour. The cache is invalidated when a new booking is made.

---

## Troubleshooting

### "No services available" message
- Make sure you've created at least one **Service** under SalonKit → Services
- Services must be **Published** (not Draft)

### "No professionals available" message
- Make sure you've created at least one **Professional**
- The professional **must be assigned** to the selected service (via the meta box on either the service or professional edit screen)
- The professional must be **Published**

### "No available slots" message
- The selected professional must have a **Weekly Schedule** set on their edit page
- The schedule must cover the selected day of the week
- The selected date must be in the future (past dates are disabled)
- Sundays are disabled by default
- All slots for that day may already be fully booked

### Form doesn't load / AJAX errors
- Make sure the plugin is **activated**
- Check browser console for JavaScript errors
- Verify `admin-ajax.php` is accessible on your site
- Check that your WordPress installation has REST API enabled

### Styling issues
- Try clearing your browser cache
- Make sure no other plugin is overriding the `.sb-wrap` CSS
- If using a cache plugin, clear its cache
- Check that Google Fonts are not blocked by your browser/network

---

## Changelog

### 2.0.0 (2026-05-29)
- Complete rewrite of Elementor Booking Widget with 140+ customization controls
- New modern design (indigo palette, cleaner spacing, better typography)
- All text editable via Elementor (40+ text controls)
- All elements hideable/showable (20 visibility controls)
- Full color control (30+ color settings)
- Full typography control (16 font levels)
- Layout & spacing controls (18 settings)
- Custom SalonKit icon set (22 SVG icons registered in Elementor)
- Vanilla JavaScript (jQuery removed — 30KB lighter)
- Skeleton loaders instead of "Loading..." text
- Keyboard navigation & focus management
- Clickable completed step indicators
- Reduced motion support (`prefers-reduced-motion`)
- Enhanced Services Grid widget with hover effects, responsive columns
- Frontend Assets class with proper enqueue system
- Icons class for Elementor icon picker integration
- Comprehensive data-attribute system for all dynamic content

### 1.0.0 (Initial Release)
- 5-step booking wizard
- Services, Professionals, Bookings CPTs
- Custom database table for bookings
- Weekly schedules with lunch breaks
- MySQL transaction-based overbooking prevention
- Email confirmation
- Elementor widgets (Booking Form + Services Grid)
- Shortcode support
- Admin dashboard widget
- jQuery-based JS
- Warm brown/gold design theme
