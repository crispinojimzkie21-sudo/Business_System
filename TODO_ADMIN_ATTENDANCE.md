# Admin Attendance Permissions for Cashier/Sales Clerk

## Status: Planning [Step 1/5]

**Goal**: Enable admin (and super admin) to check in/out cashier/sales clerk users.

**Current State**:
- `AttendanceController::adminCheckOut($userId)` exists, restricted to super admin/admin via `$user->isSuperAdmin() || $user->isAdmin()`.
- `attendance/index.blade.php` shows "Check Out" button for admins/super admins via `@if(Auth::user()->isSuperAdmin() || Auth::user()->isAdmin())`.
- Works for cashiers (role='cashier').
- User model has `isAdmin()`, `isSuperAdmin()`, `isCashier()`.

**Information Gathered**:
- Feature already implemented for admin/super admin to check out any user (including cashiers).
- Uses `route('attendance.admin-checkout', $record->user_id)`.
- Location handling, success/error messages present.
- No check-in for others; only checkout for open check-ins.

**Plan**:
1. [x] Create TODO (done).
2. Test existing feature: Login as admin, go to /attendance, verify buttons for cashier users.
3. If needed, add admin check-in method + UI in attendance/index.blade.php.
4. Ensure RoleMiddleware allows admin access to attendance.index.
5. Complete + demo command.

**Dependent Files**: AttendanceController.php (ready), attendance/index.blade.php (ready), User.php (methods ready).

**Followup**: Test: `php artisan serve` then login admin → /attendance → check buttons for cashier.
