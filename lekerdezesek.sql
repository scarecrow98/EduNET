-- kiválasztjuk azoknak a userknek az idjét, akikkel a már beszélgetsét folytattunk valaha
SELECT sender_id AS 'partner' FROM messages where receiver_id = 1
UNION
SELECT receiver_id AS 'partner' FROM messages where sender_id = 1

-- ezután végig megyünk az id-ken, és párbeszédenként választjuk ki a legutolsó üzenetet
SELECT * FROM messages WHERE receiver_id = 21 OR sender_id = 21 ORDER BY date DESC LIMIT 1