USE tank_data;
UPDATE tanks SET actions = actions + 1 WHERE health > 0 and x>-1;
