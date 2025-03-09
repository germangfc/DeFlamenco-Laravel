describe('Registro y redirección', () => {
  it('Registrar usuario y verificar en pantalla principal', () => {
    cy.visit('http://localhost/');
		cy.viewport(1380, 720);
    cy.get('#register').click();
    cy.get('#selectEntidad').select('Cliente');
    cy.get('#name').type('Alvaro');
    cy.get('#email').type('Alvarocorreo12@gmail.com');
    cy.get('#password').type('Alvaro1234');
    cy.get('#avatar').attachFile('foto.jpg');
    cy.contains('Crear Cliente').click();
    cy.get('#clickarevento').click();
    cy.get('#comprarBoton').click();
    cy.get('#carritoComprar').click();
    cy.contains('Ver Carrito', { timeout: 10000 }).click({ force: true });
    cy.get('#pagar').click();
  });
});
