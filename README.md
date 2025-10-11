# UX Archives Block for Flatsome

A custom **Flatsome UX Builder block** that displays your custom post type (e.g., Archives, Portfolio, Projects) in a filterable grid layout.  
This block integrates with Flatsome’s UX Builder and uses **Isotope.js** for category filtering, just like the built-in Portfolio element.

---

## ✨ Features
- Adds a new **UX Builder block**: `UX Archives`.
- Supports **custom post types** (default: `archives`).
- Display posts in a **responsive grid** (2–6 columns).
- **Category filter menu** above the grid (toggleable).
- Uses Flatsome’s built-in Isotope for smooth filtering.
- Lightweight and only loads scripts when shortcode/block is used.

---

## 📦 Installation
1. Download or clone this repository.
2. Place the folder in your WordPress plugins directory:
/wp-content/plugins/ux-archives-block

markdown
Copy code
3. Activate the plugin via **WordPress Dashboard → Plugins**.
4. Make sure you have the **Flatsome theme** installed and active.

---

## 🛠️ Usage

### In UX Builder
1. Open any page in **UX Builder**.
2. Add a new block → search for **“UX Archives”**.
3. Configure:
- **Posts per page**
- **Number of columns**
- **Show category filter (checkbox)**
- **Filter taxonomy** (default: `category`, or your custom taxonomy)
4. Save & publish.

### Via Shortcode
You can also use the shortcode directly:
[ux_archives posts_per_page="8" columns="4" show_filter="yes" filter_taxonomy="category"]

yaml
Copy code

---

## ⚙️ Settings

| Setting              | Type      | Default | Description                                      |
|-----------------------|-----------|---------|--------------------------------------------------|
| `posts_per_page`     | Integer   | `8`     | Number of posts to display per page              |
| `columns`            | Integer   | `4`     | Number of columns (2–6)                          |
| `show_filter`        | Yes/No    | `yes`   | Show/hide category filter menu                   |
| `filter_taxonomy`    | Taxonomy  | `category` | Taxonomy used for filters (e.g. category, custom taxonomy) |

---

## 🚀 Conditional Script Loading
Isotope is loaded **only if** the `[ux_archives]` shortcode or UX Builder block is used on the page, so it won’t slow down other pages.

---

## 📂 File Structure
ux-archives-block/
├── ux-archives-block.php # Main plugin file
├── includes/
│ ├── class-ux-archives.php # Registers block + shortcode
├── templates/
│ ├── ux-archives-template.php # Main template for block
│ └── partials/
│ └── ux-archives-item.php # Single post markup
└── README.md # This file

yaml
Copy code

---

## 📝 Notes
- Requires **Flatsome theme** (tested on v3.19+).
- Requires **WordPress 6.0+**.
- Built to mimic Flatsome Portfolio UX element.

---

## 📜 License
GPL-2.0+  
Free to use, modify, and distribute.
