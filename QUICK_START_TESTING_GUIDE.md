# Quick Start Testing Guide - Doctor Appointment Feature

## 🚀 Getting Started

### Prerequisites
All migrations and seeders have been run. The system is ready for testing.

---

## 📋 Step-by-Step Testing

### 1. Verify Database Setup

```bash
# Check if tables exist
php artisan tinker
>>> \DB::table('hospitals')->count()
>>> \DB::table('services')->where('service_type', 'appointment')->count()
```

**Expected Results:**
- 8 hospitals should exist
- 1 appointment service should exist

---

### 2. Test Hospital Management

#### Access Hospitals
1. Login to admin panel
2. Navigate to **Hospitals** in sidebar
3. You should see 8 hospitals listed

#### Create New Hospital
1. Click **Add Hospital**
2. Fill in:
   - Name: "Test Hospital"
   - Phone: "252612345678"
   - Email: "test@hospital.com"
   - Address: "Mogadishu, Somalia"
   - Status: Active
3. Click **Create Hospital**
4. Verify hospital appears in list

#### Edit Hospital
1. Click edit icon on any hospital
2. Change name or status
3. Save and verify changes

---

### 3. Test Service Configuration

#### View Appointment Service
1. Navigate to **Services** → **All Services**
2. Find "Doctor Appointment" service
3. Click **Edit**
4. Verify:
   - Service Type: "Appointment Service"
   - Custom Fields: Enabled
   - JSON configuration is present

#### Test Creating New Service with Custom Fields
1. Click **Add Service**
2. Fill basic info:
   - Name: "Lab Test"
   - Price: $50
   - Category: Select any
   - Status: Active
3. Set Service Type: "Standard Service"
4. Enable Custom Fields checkbox
5. Add simple JSON:
```json
{
  "fields": [
    {
      "key": "test_type",
      "label": "Test Type",
      "type": "text",
      "required": true
    }
  ]
}
```
6. Save and verify

---

### 4. Test Order Zone - Standard Service (Backward Compatibility)

#### Create Order with Standard Service
1. Navigate to **Order Zone**
2. Select any standard service (NOT Doctor Appointment)
3. Set quantity
4. **Verify:** Stepper shows "Step 1 of 3" (no custom fields step)
5. Enter customer phone number
6. Complete order
7. **Expected:** Order created successfully without custom fields

---

### 5. Test Order Zone - Appointment Service (Main Feature)

#### Create Appointment Order
1. Navigate to **Order Zone**
2. Select "Doctor Appointment" service
3. **Verify:** Stepper changes to "Step 1 of 4"
4. Click **Continue** or system auto-advances

#### Fill Custom Fields (Step 2)
1. **Appointment Type:** Select "Self"
2. **Verify:** Patient Name field is hidden
3. Change to "Someone Else"
4. **Verify:** Patient Name field appears
5. Fill Patient Name: "Ahmed Mohamed"
6. **Select Hospital:** Choose any hospital from dropdown
7. **Appointment Date & Time:** Select future date/time
8. Click **Continue**

#### Complete Order
1. Enter customer phone: "252612345678"
2. System should find or create customer
3. Review order preview
4. Click **Process Order**
5. **Expected:** Success message

---

### 6. Verify Appointment Created

#### Check Appointments List
1. Navigate to **Appointments** in sidebar
2. **Verify:** New appointment appears in list
3. Check details:
   - Customer name
   - Hospital name
   - Appointment time
   - Status: "Scheduled"

#### View Appointment Details
1. Click eye icon on appointment
2. **Verify all fields:**
   - Status badge
   - Hospital name
   - Appointment time
   - Customer (clickable link)
   - Appointment type
   - Patient name (if applicable)
   - Order link

---

### 7. Test Appointment Management

#### Confirm Appointment
1. On appointment detail page
2. Click **Confirm** button
3. **Verify:** Status changes to "Confirmed"
4. **Verify:** Confirmed timestamp appears

#### Cancel Appointment
1. Click **Cancel** button
2. Modal appears
3. Enter cancellation reason: "Patient requested cancellation"
4. Click **Cancel Appointment**
5. **Verify:** Status changes to "Cancelled"
6. **Verify:** Cancellation reason appears

---

### 8. Test Order Details Integration

#### View Order with Appointment
1. Navigate to **Orders** → **All Orders**
2. Find the order you just created
3. Click to view details
4. **Verify:** "Appointment Information" section appears
5. **Verify:** Shows:
   - Hospital name
   - Appointment time
   - Status badge
   - Appointment type
   - Patient name (if applicable)
   - Link to appointment details

---

### 9. Test Filters and Search

#### Hospital Filters
1. Go to **Hospitals**
2. Test search: Enter hospital name
3. Test status filter: Select "Active" or "Inactive"
4. **Verify:** Results update correctly

#### Appointment Filters
1. Go to **Appointments**
2. Test search: Enter customer name
3. Test status filter: Select different statuses
4. **Verify:** Results update correctly

---

### 10. Test Permissions

#### Test as Different User (if available)
1. Create user without hospital permissions
2. **Verify:** Hospitals menu item hidden
3. Create user without appointment permissions
4. **Verify:** Appointments menu item hidden

---

## ✅ Success Checklist

### Backward Compatibility
- [ ] Standard services work without custom fields
- [ ] Existing orders display correctly
- [ ] No errors in console

### Appointment Workflow
- [ ] Custom fields step appears for appointment service
- [ ] Conditional logic works (patient name show/hide)
- [ ] All fields validate correctly
- [ ] Appointment created successfully
- [ ] Data stored in both tables (service_field_data + appointments)

### Appointment Management
- [ ] Appointments list displays correctly
- [ ] Filters work (status, search)
- [ ] Appointment details show all information
- [ ] Confirm appointment works
- [ ] Cancel appointment works
- [ ] Status badges display correctly

### Hospital Management
- [ ] Hospital CRUD operations work
- [ ] Filters work (status, search)
- [ ] Hospital dropdown in Order Zone populated

### Integration
- [ ] Order details show appointment information
- [ ] Links between orders and appointments work
- [ ] Customer links work

---

## 🐛 Common Issues & Solutions

### Issue: Hospitals dropdown empty in Order Zone
**Solution:** Run `php artisan db:seed --class=HospitalSeeder`

### Issue: Custom fields step not appearing
**Solution:** 
1. Check service has `has_custom_fields = true`
2. Check `custom_fields_config` is valid JSON
3. Clear browser cache

### Issue: Appointment not created
**Solution:**
1. Check browser console for errors
2. Check Laravel logs: `storage/logs/laravel.log`
3. Verify OrderService has AppointmentService injected

### Issue: Permissions not working
**Solution:** Run `php artisan db:seed --class=HospitalPermissionSeeder` and `php artisan db:seed --class=AppointmentPermissionSeeder`

---

## 📊 Database Verification Queries

```sql
-- Check appointments created
SELECT * FROM appointments ORDER BY created_at DESC LIMIT 5;

-- Check field data stored
SELECT * FROM service_field_data ORDER BY created_at DESC LIMIT 10;

-- Check hospitals
SELECT * FROM hospitals WHERE status = 'active';

-- Check appointment service
SELECT * FROM services WHERE service_type = 'appointment';

-- Check orders with appointments
SELECT o.*, a.appointment_time, a.status as appointment_status
FROM orders o
JOIN order_items oi ON o.id = oi.order_id
JOIN appointments a ON oi.id = a.order_item_id
ORDER BY o.created_at DESC;
```

---

## 🎯 Performance Testing

### Load Test (Optional)
1. Create 50+ appointments
2. Test list page performance
3. Test filters with large dataset
4. Verify pagination works

---

## 📝 Test Results Template

```
Date: ___________
Tester: ___________

Backward Compatibility: ✅ / ❌
Appointment Creation: ✅ / ❌
Conditional Fields: ✅ / ❌
Field Validation: ✅ / ❌
Appointment Management: ✅ / ❌
Hospital Management: ✅ / ❌
Order Integration: ✅ / ❌
Permissions: ✅ / ❌

Issues Found:
1. _______________________
2. _______________________
3. _______________________

Notes:
_______________________
_______________________
```

---

## 🚀 Ready for Production?

Before deploying to production:
- [ ] All tests passed
- [ ] No console errors
- [ ] No Laravel log errors
- [ ] Performance acceptable
- [ ] User training completed
- [ ] Backup database
- [ ] Deploy during low-traffic period

---

**Happy Testing! 🎉**

If you encounter any issues, check:
1. Browser console (F12)
2. Laravel logs (`storage/logs/laravel.log`)
3. Network tab for API errors
4. Database for data integrity

