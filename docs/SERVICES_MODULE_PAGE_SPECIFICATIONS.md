# Services Module - Page Specifications

## Overview
This document provides detailed specifications for all pages and interfaces in the Services Module of the Caawiye Care business management system.

## 1. Services Index Page (`/admin/services`)

### Purpose
Main dashboard for managing all services with comprehensive listing, filtering, and bulk operations.

### Layout Structure
```
┌─────────────────────────────────────────────────────────────┐
│ Breadcrumb: Dashboard > Services                            │
├─────────────────────────────────────────────────────────────┤
│ Statistics Cards Row (4 cards)                             │
│ ┌─────────┐ ┌─────────┐ ┌─────────┐ ┌─────────┐           │
│ │ Total   │ │ Active  │ │Featured │ │ Average │           │
│ │Services │ │Services │ │Services │ │ Price   │           │
│ └─────────┘ └─────────┘ └─────────┘ └─────────┘           │
├─────────────────────────────────────────────────────────────┤
│ Services Management Card                                    │
│ ┌─────────────────────────────────────────────────────────┐ │
│ │ Header: "Services Management" + [Add Service] Button    │ │
│ ├─────────────────────────────────────────────────────────┤ │
│ │ Search & Filters Section                                │ │
│ │ • Search input (left)                                   │ │
│ │ • Per page selector + Filters toggle (right)           │ │
│ │ • Collapsible advanced filters (category, status, etc) │ │
│ ├─────────────────────────────────────────────────────────┤ │
│ │ Bulk Actions Bar (when items selected)                 │ │
│ ├─────────────────────────────────────────────────────────┤ │
│ │ Services Data Table                                     │ │
│ │ • Checkbox column for selection                         │ │
│ │ • Service name + description + SKU                      │ │
│ │ • Category badge                                        │ │
│ │ • Price + Cost + Profit calculation                     │ │
│ │ • Status badge                                          │ │
│ │ • Actions (View, Edit, Delete)                          │ │
│ ├─────────────────────────────────────────────────────────┤ │
│ │ Pagination Controls                                     │ │
│ └─────────────────────────────────────────────────────────┘ │
└─────────────────────────────────────────────────────────────┘
```

### Key Features
- **Statistics Cards**: Real-time metrics (total, active, featured services, average price)
- **Advanced Search**: Full-text search across name, description, SKU
- **Multi-level Filtering**: Category, status, featured flag, price range
- **Bulk Operations**: Activate, deactivate, feature, unfeature, delete
- **Sortable Columns**: Name, price, cost, status, created date
- **Responsive Design**: Mobile-friendly table with horizontal scroll

### User Interactions
- Click service name → Navigate to service details
- Click edit icon → Navigate to edit form
- Click delete icon → Show confirmation modal
- Select multiple items → Show bulk actions bar
- Apply filters → Real-time table updates via Livewire

## 2. Create Service Page (`/admin/services/create`)

### Purpose
Form interface for creating new services with comprehensive validation and user guidance.

### Layout Structure
```
┌─────────────────────────────────────────────────────────────┐
│ Breadcrumb: Dashboard > Services > Create                   │
├─────────────────────────────────────────────────────────────┤
│ Create New Service Card                                     │
│ ┌─────────────────────────────────────────────────────────┐ │
│ │ Header: "Create New Service"                            │ │
│ ├─────────────────────────────────────────────────────────┤ │
│ │ Two-Column Form Layout                                  │ │
│ │ ┌─────────────────────┐ ┌─────────────────────────────┐ │ │
│ │ │ Basic Information   │ │ Pricing & Settings          │ │ │
│ │ │ • Service Name*     │ │ • Service Price*            │ │ │
│ │ │ • Short Description │ │ • Service Cost              │ │ │
│ │ │ • Full Description  │ │ • SKU                       │ │ │
│ │ │ • Category*         │ │ • Status*                   │ │ │
│ │ │                     │ │ • Featured checkbox         │ │ │
│ │ └─────────────────────┘ └─────────────────────────────┘ │ │
│ ├─────────────────────────────────────────────────────────┤ │
│ │ Collapsible SEO Section                                 │ │
│ │ • Meta Title                                            │ │
│ │ • Meta Description                                      │ │
│ ├─────────────────────────────────────────────────────────┤ │
│ │ Form Actions                                            │ │
│ │ [Cancel] [Create Service]                               │ │
│ └─────────────────────────────────────────────────────────┘ │
└─────────────────────────────────────────────────────────────┘
```

### Form Fields Specifications

#### Basic Information Section
- **Service Name** (Required)
  - Type: Text input
  - Validation: Required, max 255 characters, unique
  - Auto-generates slug on blur
  
- **Short Description** (Optional)
  - Type: Textarea (3 rows)
  - Validation: Max 500 characters
  - Purpose: Brief summary for listings
  
- **Full Description** (Optional)
  - Type: Textarea (5 rows)
  - Validation: Max 2000 characters
  - Purpose: Detailed service information
  
- **Category** (Optional)
  - Type: Select dropdown
  - Options: Active service categories
  - Validation: Must exist in service_categories table

#### Pricing & Settings Section
- **Service Price** (Required)
  - Type: Number input (step 0.01)
  - Validation: Required, numeric, min 0
  - Format: Currency display
  
- **Service Cost** (Optional)
  - Type: Number input (step 0.01)
  - Validation: Numeric, min 0
  - Purpose: Profit margin calculation
  - Help text: "Optional: Cost for calculating profit margins"
  
- **SKU** (Optional)
  - Type: Text input
  - Validation: Unique, max 100 characters
  - Auto-generated if empty
  - Help text: "Unique identifier for the service"
  
- **Status** (Required)
  - Type: Select dropdown
  - Options: Active, Inactive, Discontinued
  - Default: Active
  
- **Featured Service** (Optional)
  - Type: Checkbox
  - Help text: "Featured services are highlighted in listings"

#### SEO Section (Collapsible)
- **Meta Title** (Optional)
  - Type: Text input
  - Validation: Max 60 characters
  - Purpose: Search engine optimization
  
- **Meta Description** (Optional)
  - Type: Textarea (3 rows)
  - Validation: Max 160 characters
  - Purpose: Search engine optimization

### User Experience Features
- **Real-time Validation**: Field-level validation with error messages
- **Auto-generation**: SKU auto-generated from service name if empty
- **Responsive Design**: Form adapts to mobile screens
- **Progress Indication**: Required fields marked with asterisk
- **Help Text**: Contextual guidance for complex fields

## 3. Edit Service Page (`/admin/services/{id}/edit`)

### Purpose
Form interface for updating existing services with current data display and change tracking.

### Layout Structure
Similar to Create page with additional features:

```
┌─────────────────────────────────────────────────────────────┐
│ Breadcrumb: Dashboard > Services > Edit                     │
├─────────────────────────────────────────────────────────────┤
│ Edit Service Card                                           │
│ ┌─────────────────────────────────────────────────────────┐ │
│ │ Header: "Edit Service" + Status Badges                 │ │
│ │ • Current status badge (Active/Inactive/Discontinued)   │ │
│ │ • Featured badge (if applicable)                        │ │
│ ├─────────────────────────────────────────────────────────┤ │
│ │ Form Layout (same as Create)                            │ │
│ │ • Pre-populated with current values                     │ │
│ │ • Profit analysis info box (if cost > 0)               │ │
│ ├─────────────────────────────────────────────────────────┤ │
│ │ Service Information Box                                 │ │
│ │ • Created date                                          │ │
│ │ • Last updated date                                     │ │
│ │ • Current slug                                          │ │
│ ├─────────────────────────────────────────────────────────┤ │
│ │ Form Actions                                            │ │
│ │ [Cancel] [Update Service]                               │ │
│ └─────────────────────────────────────────────────────────┘ │
└─────────────────────────────────────────────────────────────┘
```

### Additional Features
- **Current Status Display**: Visual indicators in header
- **Profit Analysis**: Info box showing current profit margin and percentage
- **Audit Information**: Creation and modification timestamps
- **Slug Display**: Current URL slug for reference
- **Change Detection**: Form highlights modified fields

## 4. Service Details Page (`/admin/services/{id}`)

### Purpose
Read-only detailed view of service information for review and reference.

### Layout Structure
```
┌─────────────────────────────────────────────────────────────┐
│ Breadcrumb: Dashboard > Services > [Service Name]           │
├─────────────────────────────────────────────────────────────┤
│ Service Details Card                                        │
│ ┌─────────────────────────────────────────────────────────┐ │
│ │ Header: Service Name + Action Buttons                   │ │
│ │ [Edit] [Delete] [Back to List]                          │ │
│ ├─────────────────────────────────────────────────────────┤ │
│ │ Service Information Grid                                │ │
│ │ ┌─────────────────────┐ ┌─────────────────────────────┐ │ │
│ │ │ Basic Details       │ │ Pricing Information         │ │ │
│ │ │ • Name              │ │ • Price                     │ │ │
│ │ │ • Category          │ │ • Cost                      │ │ │
│ │ │ • SKU               │ │ • Profit Margin             │ │ │
│ │ │ • Status            │ │ • Profit Percentage         │ │ │
│ │ │ • Featured          │ │                             │ │ │
│ │ └─────────────────────┘ └─────────────────────────────┘ │ │
│ ├─────────────────────────────────────────────────────────┤ │
│ │ Descriptions Section                                    │ │
│ │ • Short Description                                     │ │
│ │ • Full Description                                      │ │
│ ├─────────────────────────────────────────────────────────┤ │
│ │ SEO Information (if available)                          │ │
│ │ • Meta Title                                            │ │
│ │ • Meta Description                                      │ │
│ ├─────────────────────────────────────────────────────────┤ │
│ │ System Information                                      │ │
│ │ • Created Date                                          │ │
│ │ • Last Updated                                          │ │
│ │ • Slug                                                  │ │
│ └─────────────────────────────────────────────────────────┘ │
└─────────────────────────────────────────────────────────────┘
```

## 5. Responsive Design Specifications

### Mobile (< 768px)
- Stack form columns vertically
- Collapse table to card layout
- Hide less important columns
- Touch-friendly button sizes
- Swipe gestures for table navigation

### Tablet (768px - 1024px)
- Maintain two-column form layout
- Show essential table columns
- Responsive card grid for statistics

### Desktop (> 1024px)
- Full table display with all columns
- Side-by-side form sections
- Hover effects and tooltips
- Keyboard navigation support

## 6. Accessibility Features

### WCAG 2.1 AA Compliance
- **Keyboard Navigation**: All interactive elements accessible via keyboard
- **Screen Reader Support**: Proper ARIA labels and descriptions
- **Color Contrast**: Minimum 4.5:1 ratio for text
- **Focus Indicators**: Clear visual focus states
- **Error Handling**: Descriptive error messages with field association

### Semantic HTML
- Proper heading hierarchy (h1, h2, h3)
- Form labels associated with inputs
- Table headers with scope attributes
- Landmark regions (main, nav, aside)

## 7. Performance Considerations

### Page Load Optimization
- **Lazy Loading**: Table data loaded on demand
- **Image Optimization**: Responsive images with proper sizing
- **CSS/JS Minification**: Compressed assets for faster loading
- **Caching**: Browser and server-side caching strategies

### User Experience
- **Loading States**: Skeleton screens during data fetching
- **Error Boundaries**: Graceful error handling and recovery
- **Offline Support**: Basic functionality when connection is poor
- **Progressive Enhancement**: Core functionality works without JavaScript

## 8. Security Specifications

### Authorization
- **Role-based Access**: Different permissions for different user roles
- **Route Protection**: Middleware checks for proper permissions
- **CSRF Protection**: All forms include CSRF tokens
- **Input Validation**: Server-side validation for all inputs

### Data Protection
- **SQL Injection Prevention**: Parameterized queries and ORM usage
- **XSS Protection**: Output escaping and Content Security Policy
- **File Upload Security**: Type validation and secure storage
- **Rate Limiting**: API endpoint protection against abuse

This comprehensive specification ensures the Services Module provides a professional, accessible, and secure interface for managing business services within the Caawiye Care system.
