describe("template spec", () => {
	it("login de admin full width device", () => {
		cy.visit("http://localhost");
		cy.viewport(1380, 720);
		cy.get("#login").should("be.visible").click();
		cy.url().should("include", "/login");
		cy.get("#email").type("admin@example.com");
		cy.get("#password").type("12345678");
		cy.get("#submit").click({ force: true });
		cy.url().should("include", "/");
	});
});
