# TODO: Fix SuperAdminController $admins Error

## Plan Steps:
- [x] 1. Edit `app/Http/Controllers/SuperAdminController.php`: Add `$admins = User::where('role', 'admin')->get();` in `accessControl()` before return view.
- [x] 2. Clear Laravel caches: `php artisan cache:clear & php artisan config:clear & php artisan view:clear` (Windows CMD syntax)
- [x] 3. Test access-control page loads without error.
- [x] 4. Verify $admins data in view.

**Status:** ✅ COMPLETE - $admins undefined error fixed!
