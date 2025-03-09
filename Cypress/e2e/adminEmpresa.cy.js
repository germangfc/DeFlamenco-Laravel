describe("template spec", () => {

  Cypress.on('uncaught:exception', (err, runnable) => {

    console.error(err);

    return false;
  });

  it("login de admin crear empresa", () => {
    cy.visit("http://localhost");
    cy.viewport(1380, 720);
    cy.get("#login").should("be.visible").click();
    cy.url().should("include", "/login");
    cy.get("#email").type("admin@example.com");
    cy.get("#password").type("12345678");
    cy.get("#submit").click({ force: true });
    cy.get("#empresa").click();
    cy.get("#empresacrear").click();
    cy.get("#imagen").selectFile("cypress/fixtures/foto.jpg", { force: true });
    cy.get('#name').type('CompanyDevelopers');
    cy.get('#cif').should('be.visible').type('B12345678'); 
    cy.get('#direccion').type('Calle Mexico');
    cy.get('#telefono').type('623456789');
    cy.get('#email').type('companydevelopers@gmail.com');
    cy.get('#cuentaBancaria').type('ES8000755174538626917678');
    cy.get('#password').type('contraseñasegura1234');
    cy.get('#registrarEmpresa').click();
    cy.get('#buscadorEmpresas').type('companydevelopers@gmail.com');    
    cy.get('.btn.btn-primary').click();
    cy.get('#eliminarBoton').click();
  });
});
