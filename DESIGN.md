---
version: alpha
name: AgencySuit
description: A focused mobile worklist for independent Thai real-estate agents.
colors:
  canvas: "#f4f2ed"
  surface: "#ffffff"
  ink: "#18211f"
  muted: "#66716c"
  line: "#dde1dd"
  primary: "#1e5b57"
  primary-strong: "#154744"
  primary-soft: "#e6f2ee"
  overdue: "#cf6b52"
  overdue-soft: "#fbe9e3"
  danger: "#b34f43"
  scroll-thumb: "#b8c5bf"
typography:
  ui:
    fontFamily: "Instrument Sans, Noto Sans Thai, Leelawadee UI, Tahoma, system-ui, sans-serif"
rounded:
  control: "11px"
  surface: "16px"
  sheet: "19px"
spacing:
  page-gutter: "1rem"
  section-gap: "1rem"
components:
  navigation: {}
  work-row: {}
  list-row: {}
  bottom-sheet: {}
  confirm-dialog: {}
  form-field: {}
---

# AgencySuit Design System

## Overview

AgencySuit should feel like a small, dependable work notebook carried by a solo agent. The product register is practical: the next call, appointment, property, and client must be readable in one glance on a phone. Its signature is the grouped daily worklist with a restrained overdue rail and a single floating Quick Add action. It must not resemble a KPI dashboard, spreadsheet, or generic admin template.

The product and workflow sources are [AGENTS.md](AGENTS.md) and [skills/uxui/SKILL.md](skills/uxui/SKILL.md). The primary language is Thai. Mobile is primary; desktop receives a centered, readable application column.

The runtime source of truth for tokens is `resources/css/app.css` (`--as-*`). This document mirrors its accepted values and explains their use; it does not generate CSS. Shared Blade layouts and the `x-icon` component consume those classes. Change the CSS and this document together when a durable token changes.

## Colors

The warm canvas separates work sections without decoration. White surfaces carry content; ink and muted text form the hierarchy. Deep teal identifies primary actions and active navigation. Coral is reserved for overdue work; danger is reserved for destructive or error states. Status meaning must always have text alongside color. High contrast mode uses system colors.

## Typography

Instrument Sans leads Latin and numeric UI; the listed Thai-capable fallbacks carry Thai glyphs. Page titles are compact and strong; section headings and row titles use weight before additional ornament. Body text starts at 16px, with smaller metadata reserved for secondary information. Dates, prices, and match percentages remain readable without relying on a badge color.

## Layout

The app column is at most 34rem wide. A 1rem mobile gutter, compact cards, and full-width primary actions support one-hand use. The four-destination bottom navigation stays visible, with safe-area padding. Quick Add opens from the bottom. Long detail pages scroll naturally, with enough bottom space that the navigation never covers the last action. No desktop table is the primary mobile layout.

## Elevation & Depth

Static content uses borders and surface contrast. Only the floating Quick Add control and its modal sheet receive notable shadows. There are no gradients, glass effects, or decorative depth on routine work cards.

## Shapes

Controls use an 11px radius and content surfaces use 16px. The sheet rounds only its upper corners. The floating add control is circular. Simple 1.8px stroked icons support labels rather than replacing them.

## Components

Today uses grouped work sections: overdue, appointments, then due follow-ups. Property and client lists use compact rows with key details and clear navigation. Primary buttons use teal; secondary buttons are neutral outlines. Inputs have persistent labels, visible focus, and inline validation. The Quick Add sheet contains the four actions defined in AGENTS.md. Empty states say what is missing and offer the next real action.

Hover, pressed, focus, disabled, and error states should remain discernible. Motion is limited to control feedback and respects reduced-motion preferences. Native select/date controls are accepted for the existing short forms; their platform popups are not presented as custom UI.

## Do's and Don'ts

- Do surface the agent's next action before summary statistics.
- Do keep a property or client row scannable at 360px.
- Don't add navigation destinations for contextual features.
- Don't use large photos, dashboard cards, tables, or decorative effects to displace essential work.
