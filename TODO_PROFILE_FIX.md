# Profile Features for Admin/Super Admin (Same as Cashier)

## Status: In Progress [Step 1/6]

**Goal**: Add profile navigation links and "Your Information" sections with password change links to admin/super admin dashboards, matching cashier features (display-only info + pw change, no email edit).

**Steps**:
- [x] 1. Create this TODO.md
- [ ] 2. Update resources/views/dashboard/admin.blade.php (add nav Profile link + enhance info section with pw link)
- [ ] 3. Update resources/views/dashboard/super_admin.blade.php (add nav Profile link + add full info section)
- [ ] 4. Test profile access for admin/superadmin roles (login, click profile, change pw)
- [ ] 5. Verify styling/theme consistency (blue for admin, red for superadmin)
- [ ] 6. Update TODO.md complete + attempt_completion

**Notes**:
- Uses existing route('dashboard.profile') -> profile.blade.php (pw change only)
- No new controllers/routes needed
- Clear caches if UI issues: php artisan route:clear config:clear view:clear
