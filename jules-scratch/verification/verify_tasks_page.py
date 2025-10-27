from playwright.sync_api import Page, expect

def test_task_management_page(page: Page):
    # 1. Arrange: Go to the login page.
    print("Navigating to the signup page.")
    page.goto("http://localhost:8000/signup.php", wait_until="load")
    print("On the signup page.")

    # 2. Act: Log in as an admin.
    print("Filling in the login form.")
    page.get_by_role("textbox", name="Username", exact=True).fill("admin")
    page.locator("#login").get_by_role("textbox", name="Password").fill("admin")
    print("Clicking the login button.")
    page.locator("#login button[type='submit']").click()
    print("Login button clicked.")
    page.wait_for_timeout(5000) # Wait for 5 seconds

    # 3. Act: Navigate to the first project's tasks page.
    print("Navigating to the project page.")
    page.get_by_role("link", name="Project").click()
    print("On the project page.")
    print("Clicking the first project link.")
    page.locator(".project-card-link").first.click()
    print("First project link clicked.")

    # 4. Assert: Confirm the navigation was successful.
    print("Checking the page title.")
    expect(page).to_have_title("Project Tasks")
    print("Page title is correct.")

    # 5. Screenshot: Capture the final result for visual verification.
    print("Taking a screenshot.")
    page.screenshot(path="jules-scratch/verification/tasks-page.png")
    print("Screenshot taken.")
