describe("template spec", () => {
	it("register de cliente full width device", () => {
		cy.visit("http://localhost");
		cy.viewport(1380, 720);
		cy.get("#register").should("be.visible").click();
		cy.url().should("include", "/register");
		cy.get("#selectEntidad").select("cliente").should("have.value", "cliente");
		cy.get("#name").should("be.visible").type("deFlamenco");
		cy.get("#email").should("be.visible").type("Tablaopass.noreply@gmail.com");
		cy.get("#password").should("be.visible").type("12345678");
		cy.get("#avatar").attachFile("foto.jpg");
		cy.get("#crearCliente").click();
		cy.url().should("include", "/");
		cy.get("#imagenPerfil").should("be.visible").click();
		cy.get("#cerrarSesioncliente").click();
		cy.url().should("include", "/");
		cy.get("#register").should("be.visible").click();
	});

	it("register de cliente mobile device", () => {
		cy.visit("http://localhost");
		cy.viewport(375, 667);
		cy.visit("http://localhost");
		cy.get("#hamburguer").should("be.visible").click();
		cy.get("#registerhamburger").should("be.visible").click();
		cy.url().should("include", "/register");
		cy.get("#selectEntidad").select("cliente").should("have.value", "cliente");
		cy.get("#name").should("be.visible").type("deFlamenco");
		cy.get("#email").should("be.visible").type("Tablaopass2.noreply@gmail.com");
		cy.get("#password").should("be.visible").type("12345678");
		cy.get("#avatar").attachFile("foto.jpg");
		cy.get("#crearCliente").click();
		cy.url().should("include", "/");
		cy.get("#imagenPerfil").should("be.visible").click();
		cy.get("#cerrarSesioncliente").click();
		cy.url().should("include", "/");
		cy.get("#hamburguer").should("be.visible").click();
		cy.get("#registerhamburger").should("be.visible").click();
	});
});
