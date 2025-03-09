describe('template spec', () => {
  it("login de admin, poder crear usuario, buscarlo y eliminarlo", () => {
    cy.visit("http://localhost");
    cy.viewport(1380, 720);
    cy.get('#register').click();
    cy.get('#selectEntidad').select('Cliente');
    cy.get('#name').type('Raúl');
    cy.get('#email').type('raul@example.com');
    cy.get('#password').type('raul1234');
    cy.get('#dni').type('49450649M');
    cy.get('#foto_dni').selectFile("cypress/fixtures/foto.jpg", { force: true });
    cy.get('#crearCliente').click();
    cy.get('#imagenPerfil').click();
    cy.contains('Cerrar Sesión').click();
    cy.get('#login').click();
    cy.get('#email').type('admin@example.com');
    cy.get('#password').type('12345678');
    cy.get('#submit').click();
    cy.get('#imagenPerfil').click();
    cy.contains('Clientes').click();
    cy.get('#buscadorClientes').type('Raúl');
    cy.get('#buscadorBotonClientes').click();
    cy.get('#eliminarEmpresa').click();
    cy.get('#buscadorClientes').type('Raúl');
    cy.get('#buscadorBotonClientes').click();
  })
})