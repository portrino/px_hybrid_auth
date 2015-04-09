.. ==================================================
.. FOR YOUR INFORMATION
.. --------------------------------------------------
.. -*- coding: utf-8 -*- with BOM.

.. include:: ../Includes.txt


.. _for-developers:

For Developers
==============

The *PxHybridAuth* extension is designed in a way which should make it easy to integrate other social providers by developers.


New social provider
^^^^^^^^^^^^^^^^^^

To extend *px_hybrid_auth* with a new social provider there are several steps  necessary:

1. create a new identity model in ``\PxHybridAuth\Domain\Model\Identity``
2. xx
3. Signal slot ..

| ``EXTBASE_TYPE = 'Tx_PxHybridAuth_Domain_Model_Identity_[Yoursocialprovider]Identity';``
| is used to define the social provider
| ([Yoursocialprovider] must be in lower case with upper case first)

