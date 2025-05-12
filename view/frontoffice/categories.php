<div class="quantity-selector">
                  <label for="qty-<?php echo $row['id']; ?>">disponibilite :</label>
                  <?php if ($disponibilite): ?>
                    <i class="fas fa-check-circle" style="color:green;" title="Disponible"></i>
                  <?php else: ?>
                    <i class="fas fa-times-circle" style="color:red;" title="Indisponible"></i>
                  <?php endif; ?>
                </div>

                <div class="quantity-selector">
                  <label for="qty-<?php echo $row['id']; ?>">Quantité :</label>
                  <input
                    type="number"
                    id="qty-<?php echo $row['id']; ?>"
                    name="qty-<?php echo $row['id']; ?>"
                    min="1"
                    max="10"
                    value="1"
                  />
                  <a class="add-to-cart" href="http://localhost/gamy/view/front%20office/commande.php?image=<?php echo urlencode($row['image']); ?>&nom=<?php echo urlencode($row['nom']); ?>&prix=<?php echo urlencode($row['prix']); ?>">
  Add to Cart
</a>